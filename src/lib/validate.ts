import { db } from "./db";
import { checkImage } from "./uploads";
import type { Field } from "./resources";

export type V = { data: Record<string, any>; files: Record<string, File>; values: Record<string, any>; errors: string[] };
export function validate(f: FormData, fields: Field[], o: { table?: string; id?: number; isNew?: boolean } = {}): V {
  const data: Record<string, any> = {}, files: Record<string, File> = {}, values: Record<string, any> = {}, errors: string[] = [];
  for (const fl of fields) {
    if (fl.ro) continue;
    const n = fl.name, a = fl.label.split(" (")[0];
    if (fl.type === "image") {
      const file = f.get(n);
      if (file instanceof File && file.size > 0) {
        const e = checkImage(file, a, fl.mimes, fl.maxKb);
        if (e) errors.push(e); else files[n] = file;
      } else if (fl.reqNew && o.isNew) errors.push(`${a} wajib diisi.`);
      continue;
    }
    if (fl.type === "checkbox") { data[n] = values[n] = f.getAll(n).includes("1") ? 1 : 0; continue; }
    const raw = fl.type === "password" ? String(f.get(n) ?? "") : String(f.get(n) ?? "").trim();
    if (fl.type !== "password") values[n] = raw;
    if (!raw) {
      if (fl.required || (fl.reqNew && o.isNew)) errors.push(`${a} wajib diisi.`);
      data[n] = fl.def ?? null;
      continue;
    }
    if (fl.max && raw.length > fl.max) errors.push(`${a} maksimal ${fl.max} karakter.`);
    if (fl.minLen && raw.length < fl.minLen) errors.push(`${a} minimal ${fl.minLen} karakter.`);
    if (fl.type === "number") {
      if (!/^-?\d+$/.test(raw)) errors.push(`${a} harus berupa angka bulat.`);
      else {
        const x = Number(raw);
        if (fl.min != null && x < fl.min) errors.push(`${a} minimal ${fl.min}.`);
        if (fl.maxv != null && x > fl.maxv) errors.push(`${a} maksimal ${fl.maxv}.`);
        data[n] = x;
        continue;
      }
    } else if (fl.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(raw)) errors.push(`${a} harus berupa alamat email yang valid.`);
    else if (fl.type === "select" && !(raw in (fl.options ?? {}))) errors.push(`${a} tidak valid.`);
    else if (fl.type === "slug") {
      if (!/^[A-Za-z0-9_-]+$/.test(raw)) errors.push(`${a} hanya boleh berisi huruf, angka, strip, dan garis bawah.`);
      else if (o.table && db.prepare(`select 1 from ${o.table} where slug=? and id<>?`).get(raw, o.id ?? 0)) errors.push(`${a} sudah dipakai.`);
    }
    if (fl.url) { try { if (!/^https?:$/.test(new URL(raw).protocol)) throw 0; } catch { errors.push(`${a} harus berupa URL yang valid.`); } }
    if (fl.pattern && !new RegExp(fl.pattern[0]).test(raw)) errors.push(fl.pattern[1]);
    if (fl.unique && o.table && db.prepare(`select 1 from ${o.table} where ${n}=? and id<>?`).get(raw, o.id ?? 0)) errors.push(`${a} sudah dipakai.`);
    data[n] = raw;
  }
  return { data, files, values, errors };
}
