import { defineConfig } from 'cypress';
import { execFileSync } from 'node:child_process';
import { baseUrl, environment, php, root } from './tests/Browser/environment.mjs';

export default defineConfig({
    viewportWidth: 1280,
    viewportHeight: 900,
    video: false,
    e2e: {
        baseUrl,
        supportFile: false,
        setupNodeEvents(on, config) {
            if (config.baseUrl !== baseUrl) throw new Error('UI tests must use the isolated local server.');
            const database = action => JSON.parse(execFileSync(php, ['tests/Browser/database.php', action], {
                cwd: root, env: environment, encoding: 'utf8', windowsHide: true,
            }));
            on('task', { resetDatabase: () => database('reset'), databaseState: () => database('state') });
        },
    },
});
