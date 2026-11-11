import { spawn, execFileSync } from 'node:child_process';
import net from 'node:net';
import path from 'node:path';
import { baseUrl, environment, php, root } from './environment.mjs';

// Electron-based editors may export this flag; Cypress needs a browser process.
delete process.env.ELECTRON_RUN_AS_NODE;
const { default: cypress } = await import('cypress');

// Refuse to attach to an unrelated application already running on this port.
await new Promise((resolve, reject) => {
    const probe = net.createServer();
    probe.once('error', reject);
    probe.listen(8877, '127.0.0.1', () => probe.close(resolve));
});
execFileSync(php, ['tests/Browser/database.php', 'reset'], { cwd: root, env: environment, windowsHide: true });
const server = spawn(php, ['-S', '127.0.0.1:8877', '-t', path.join(root, 'public'), path.join(root, 'vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php')], {
    cwd: path.join(root, 'public'), env: environment, windowsHide: true, stdio: ['ignore', 'ignore', 'pipe'],
});
let log = '';
server.stderr.on('data', data => { log = (log + data).slice(-8000); });
const cleanup = () => server.kill();
process.once('exit', cleanup);
process.once('SIGINT', () => { cleanup(); process.exit(130); });
process.once('SIGTERM', () => { cleanup(); process.exit(143); });
try {
    let ready = false;
    for (let i = 0; i < 50; i++) {
        if (server.exitCode !== null) throw new Error(log);
        try { if ((await fetch(baseUrl + '/up')).ok) { ready = true; break; } } catch {}
        await new Promise(resolve => setTimeout(resolve, 200));
    }
    if (!ready) throw new Error('Test server failed to start. ' + log);
    if (process.argv.includes('--open')) {
        await cypress.open({ project: root, e2e: true });
    } else {
        const result = await cypress.run({ project: root, browser: 'electron' });
        process.exitCode = result.failures || result.totalFailed ? 1 : 0;
    }
} finally {
    cleanup();
}
