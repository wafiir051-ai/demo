"use client";
import { Fragment, useActionState, useState } from "react";
import Link from "next/link";
import type { Field } from "@/lib/resources";

type St = { errors: string[]; values: Record<string, any> } | null;
type Extras = { facilities?: { id: number; name: string; icon: string | null }[]; selected?: number[]; images?: { id: number; path: string }[]; itemsText?: string };

function Img({ name, accept, cur, label }: { name: string; accept: string; cur?: string | null; label: string }) {
  const [p, setP] = useState<string | null>(null);
  const src = p ?? (cur ? `/uploads/${cur}` : null);
  return (<>
    <input type="file" name={name} accept={accept} onChange={e => { const f = e.target.files?.[0]; setP(f ? URL.createObjectURL(f) : null); }} />
    {src && <img className="prev" src={src} alt={`Pratinjau ${label}`} />}
  </>);
}

export default function ResForm({ action, fields, row, hidden = {}, extras = {}, cancel, submit = "Simpan" }: {
  action: (prev: St, f: FormData) => Promise<St>; fields: Field[]; row: Record<string, any>; hidden?: Record<string, string>; extras?: Extras; cancel: string; submit?: string;
}) {
  const [st, act, pending] = useActionState(action, null);
  const isNew = !row.id;
  const val = (fl: Field) => (st ? st.values[fl.name] ?? "" : row[fl.name] ?? fl.def ?? "");
  const accept = (fl: Field) => (fl.mimes?.includes("ico") ? "image/png,image/jpeg,image/webp,image/x-icon" : "image/png,image/jpeg,image/webp");
  const picked: number[] = st ? st.values.facility_ids ?? [] : extras.selected ?? [];

  const field = (fl: Field) => (
    <label key={fl.name} className={fl.type === "checkbox" ? "chk" : undefined}>
      {fl.type === "checkbox" ? (<>
        <input type="hidden" name={fl.name} value="0" />
        <input type="checkbox" name={fl.name} value="1" defaultChecked={st ? !!st.values[fl.name] : isNew ? true : !!row[fl.name]} /> {fl.label}
      </>) : (<>
        <span>{fl.label}</span>
        {fl.ro ? <input type="email" value={row[fl.name] ?? ""} disabled readOnly />
          : fl.type === "textarea" ? <textarea name={fl.name} rows={fl.rows ?? 5} defaultValue={val(fl)} />
          : fl.type === "select" ? <select name={fl.name} defaultValue={String(val(fl))}>{Object.entries(fl.options ?? {}).map(([k, l]) => <option key={k} value={k}>{l}</option>)}</select>
          : fl.type === "number" ? <input type="number" name={fl.name} defaultValue={val(fl)} min={fl.min ?? 0} />
          : fl.type === "image" ? <Img name={fl.name} accept={accept(fl)} cur={row[fl.name]} label={fl.label} />
          : fl.type === "password" ? <input type="password" name={fl.name} autoComplete={fl.name === "current_password" ? "current-password" : "new-password"} />
          : <input type={fl.type === "email" ? "email" : fl.type === "color" ? "color" : fl.url ? "url" : "text"} name={fl.name} defaultValue={val(fl)} required={fl.htmlRequired} />}
      </>)}
    </label>
  );

  const blocks: Field[][] = [];
  for (const fl of fields) {
    const last = blocks[blocks.length - 1];
    if (fl.two && !fl.section && last && last[last.length - 1].two) last.push(fl); else blocks.push([fl]);
  }

  return (
    <form className="card form" action={act}>
      {Object.entries(hidden).map(([k, v]) => <input key={k} type="hidden" name={k} value={v} />)}
      {st?.errors.length ? <div className="alert err"><ul>{st.errors.map((e, i) => <li key={i}>{e}</li>)}</ul></div> : null}
      {blocks.map(b => (
        <Fragment key={b[0].name}>
          {b[0].section && <h2>{b[0].section}</h2>}
          {b[0].two ? <div className="two">{b.map(field)}</div> : field(b[0])}
        </Fragment>
      ))}
      {extras.facilities && (<>
        <fieldset>
          <legend>Fasilitas yang tersedia di unit ini</legend>
          {extras.facilities.length ? extras.facilities.map(x => (
            <label className="chk" key={x.id}><input type="checkbox" name="facility_ids" value={x.id} defaultChecked={picked.includes(x.id)} /> {x.icon} {x.name}</label>
          )) : <p className="muted">Belum ada fasilitas. Tambahkan dulu di menu Fasilitas.</p>}
        </fieldset>
        <fieldset>
          <legend>Galeri foto tambahan (maks 10 file per simpan, 3 MB per file)</legend>
          <input type="file" name="gallery_images" multiple accept="image/png,image/jpeg,image/webp" />
          {extras.images && extras.images.length > 0 && (
            <div className="imggrid">{extras.images.map(i => (
              <label className="chk imgcell" key={i.id}><img src={`/uploads/${i.path}`} alt="Foto galeri" loading="lazy" /><span><input type="checkbox" name="remove_images" value={i.id} /> Hapus</span></label>
            ))}</div>
          )}
        </fieldset>
      </>)}
      {extras.itemsText !== undefined && (
        <label><span>Yang termasuk dalam paket (satu item per baris)</span>
          <textarea name="items_text" rows={6} defaultValue={st ? st.values.items_text ?? "" : extras.itemsText} /></label>
      )}
      <div className="formacts"><button className="btn" type="submit" disabled={pending}>{submit}</button><Link className="btn ghost" href={cancel}>Batal</Link></div>
    </form>
  );
}
