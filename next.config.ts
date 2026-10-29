import type { NextConfig } from "next";
const nextConfig: NextConfig = {
  cacheComponents: false, // halaman membaca database, jadi harus dinamis
  allowedDevOrigins: ["127.0.2.2"],
  serverExternalPackages: ["node:sqlite"],
  experimental: { serverActions: { bodySizeLimit: "30mb" } }, // upload foto dari admin (maks 3 MB per file, hingga 10 file)
};
export default nextConfig;
