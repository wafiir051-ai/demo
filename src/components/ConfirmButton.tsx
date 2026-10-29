"use client";
export default function ConfirmButton({ children, text, className = "btn danger" }: { children: React.ReactNode; text: string; className?: string }) {
  return <button className={className} onClick={e => { if (!confirm(text)) e.preventDefault(); }}>{children}</button>;
}
