export default async function Ok({ params }: { params: Promise<{ code: string }> }) {
  const { code } = await params;
  return <div className="py-16 text-center"><h1 className="text-2xl font-bold text-emerald-900">Reservasi terkirim</h1>
    <p className="mt-2">Kode reservasi Anda:</p><p className="text-3xl font-mono font-bold">{code}</p>
    <p className="mt-2 text-stone-600">Kami akan menghubungi Anda untuk konfirmasi.</p></div>;
}
