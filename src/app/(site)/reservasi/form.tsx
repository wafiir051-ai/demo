"use client";
import { useActionState } from "react";
import { reserve } from "@/app/actions";
export default function Form({ rooms, acc }: { rooms: { id: number; name: string }[]; acc?: string }) {
  const [state, action, pending] = useActionState(reserve, null as { error: string } | null);
  const c = "w-full rounded border p-2";
  return (
    <form action={action} className="max-w-lg space-y-3">
      {state?.error && <p className="rounded bg-red-100 p-2 text-red-800">{state.error}</p>}
      <input name="guest_name" placeholder="Nama lengkap *" className={c} />
      <input name="phone" placeholder="No. WhatsApp *" className={c} />
      <input name="email" type="email" placeholder="Email" className={c} />
      <select name="accommodation_id" defaultValue={acc} className={c}><option value="">Pilih akomodasi</option>
        {rooms.map(r => <option key={r.id} value={r.id}>{r.name}</option>)}</select>
      <div className="flex gap-2"><input name="check_in" type="date" className={c} /><input name="check_out" type="date" className={c} /></div>
      <input name="guest_count" type="number" min={1} placeholder="Jumlah tamu *" className={c} />
      <textarea name="notes" placeholder="Permintaan khusus" className={c} />
      <button disabled={pending} className="rounded bg-emerald-900 px-6 py-2 text-white">{pending ? "Mengirim..." : "Kirim reservasi"}</button>
    </form>
  );
}
