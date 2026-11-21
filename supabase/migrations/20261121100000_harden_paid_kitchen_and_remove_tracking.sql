-- Final hardening for Arana Space order operations.
create or replace function public.guard_order_updates()
returns trigger language plpgsql set search_path=public as $$
declare
  v_role text;
  v_payment_context text := coalesce(current_setting('app.payment_confirmation', true), '');
  v_create_context text := coalesce(current_setting('app.order_creation', true), '');
begin
  if new.payment_method is distinct from old.payment_method and v_create_context <> 'create' then
    raise exception 'Metode pembayaran tidak dapat diubah setelah pesanan dibuat';
  end if;
  if (new.paid_at is distinct from old.paid_at or new.paid_by is distinct from old.paid_by or new.payment_status is distinct from old.payment_status)
     and v_payment_context <> 'cash_confirm' and v_create_context <> 'create' then
    raise exception 'Status pembayaran hanya dapat diubah melalui proses konfirmasi resmi';
  end if;
  if new.order_status is distinct from old.order_status then
    select role into v_role from public.profiles where id = (select auth.uid());
    if v_role = 'kitchen' then
      if old.order_status in ('pending','confirmed') then
        if old.payment_status <> 'paid' then raise exception 'Pesanan harus lunas sebelum masuk antrean dapur'; end if;
        if new.order_status <> 'preparing' then raise exception 'Pesanan baru harus dimulai ke status sedang dibuat'; end if;
      elsif old.order_status = 'preparing' then
        if new.order_status <> 'completed' then raise exception 'Pesanan yang diproses hanya dapat diselesaikan melalui tombol Siap Disajikan'; end if;
      else raise exception 'Pesanan sudah ditutup';
      end if;
    elsif v_role in ('admin','cashier') then
      if new.order_status <> 'cancelled' or old.order_status in ('completed','cancelled') then
        raise exception 'Admin dan kasir hanya dapat membatalkan pesanan aktif';
      end if;
    else raise exception 'Tidak memiliki izin mengubah pesanan';
    end if;
  end if;
  return new;
end;
$$;

create or replace function public.kitchen_update_order(p_order_id bigint, p_status text)
returns jsonb language plpgsql security definer set search_path=public as $$
declare v_role text; v_order public.orders%rowtype;
begin
  select role into v_role from public.profiles where id=(select auth.uid());
  if coalesce(v_role,'') <> 'kitchen' then raise exception 'Hanya petugas dapur yang dapat memperbarui proses pesanan'; end if;
  if p_status not in ('preparing','ready') then raise exception 'Status dapur tidak valid'; end if;
  select * into v_order from public.orders where id=p_order_id for update;
  if not found then raise exception 'Pesanan tidak ditemukan'; end if;
  if v_order.payment_status <> 'paid' then raise exception 'Pesanan harus lunas sebelum masuk antrean dapur'; end if;
  if v_order.order_status in ('completed','cancelled') then raise exception 'Pesanan sudah ditutup'; end if;
  if p_status='preparing' then
    if v_order.order_status not in ('pending','confirmed') then raise exception 'Status pesanan tidak dapat diubah'; end if;
    update public.orders set order_status='preparing' where id=p_order_id;
  else
    if v_order.order_status <> 'preparing' then raise exception 'Mulai proses pesanan terlebih dahulu'; end if;
    update public.orders set order_status='completed',ready_at=now() where id=p_order_id;
  end if;
  return jsonb_build_object('success',true,'order_status',case when p_status='ready' then 'completed' else 'preparing' end);
end;
$$;

revoke all on function public.kitchen_update_order(bigint,text) from public, anon;
grant execute on function public.kitchen_update_order(bigint,text) to authenticated;
drop function if exists public.lookup_customer_order(text,text);
create index if not exists orders_paid_kitchen_queue_idx on public.orders(payment_status,order_status,created_at desc);
create index if not exists orders_paid_cashier_shift_idx on public.orders(paid_by,paid_at) where payment_status='paid';
