// Dev server lokal tanpa dependency. Meniru perilaku Vercel:
// - rewrites dari vercel.json (/features, /pricing, dst -> /index.html)
// - path yang tidak ada -> 404.html dengan status 404
// Jalankan: node dev.js  (default port 5500, atau: PORT=3000 node dev.js)
const http = require('http');
const fs = require('fs');
const path = require('path');

const ROOT = __dirname;
const PORT = process.env.PORT || 5500;
const TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.ico': 'image/x-icon',
  '.woff2': 'font/woff2',
  '.txt': 'text/plain; charset=utf-8'
};

let rewrites = [];
try {
  rewrites = JSON.parse(fs.readFileSync(path.join(ROOT, 'vercel.json'), 'utf8')).rewrites || [];
} catch (e) {}

function send(res, status, file) {
  fs.readFile(file, function (err, data) {
    if (err) {
      res.writeHead(500, { 'Content-Type': 'text/plain; charset=utf-8' });
      return res.end('Server error');
    }
    res.writeHead(status, {
      'Content-Type': TYPES[path.extname(file).toLowerCase()] || 'application/octet-stream',
      'Cache-Control': 'no-store'
    });
    res.end(data);
  });
}

function notFound(res) {
  const f404 = path.join(ROOT, '404.html');
  if (fs.existsSync(f404)) return send(res, 404, f404);
  res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
  res.end('404 Not Found');
}

http.createServer(function (req, res) {
  let p;
  try { p = decodeURIComponent(req.url.split('?')[0]); } catch (e) { return notFound(res); }
  const clean = p.replace(/\/+$/, '') || '/';

  const rw = rewrites.find(function (r) { return r.source === clean; });
  if (rw) return send(res, 200, path.join(ROOT, rw.destination));

  const rel = clean === '/' ? '/index.html' : clean;
  const file = path.normalize(path.join(ROOT, rel));
  if (file.indexOf(ROOT) !== 0) return notFound(res);

  fs.stat(file, function (err, st) {
    if (!err && st.isFile()) return send(res, 200, file);
    notFound(res);
  });
}).listen(PORT, function () {
  console.log('Dev server jalan di http://localhost:' + PORT);
});