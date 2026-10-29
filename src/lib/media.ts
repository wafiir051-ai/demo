// Placeholder SVG yang sama dengan Media::url() di Laravel.
export function placeholder(label: string) {
  const esc = label.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2f4a3a"/><stop offset="1" stop-color="#8aa28a"/></linearGradient></defs><rect width="1200" height="800" fill="url(#g)"/><text x="600" y="410" font-family="Georgia,serif" font-size="44" fill="#ffffffcc" text-anchor="middle">${esc}</text></svg>`;
  return "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg);
}
// Foto yang diunggah lewat admin tersimpan di folder uploads/ dan dilayani lewat /uploads/...
// Bila belum ada foto, pakai placeholder.
export function img(path: string | null | undefined, label: string) {
  return path ? `/uploads/${path}` : placeholder(label);
}
