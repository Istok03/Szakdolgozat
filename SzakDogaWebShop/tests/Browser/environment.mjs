import path from 'node:path';
import fs from 'node:fs';
import { fileURLToPath } from 'node:url';

export const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
export const directory = path.join(root, 'storage/framework/cypress');
export const baseUrl = 'http://127.0.0.1:8877';
export const php = process.env.PHP_BINARY || (fs.existsSync('C:/xampp/php/php.exe') ? 'C:/xampp/php/php.exe' : 'php');
fs.mkdirSync(directory, { recursive: true });
// Both the server and CLI fixtures must use exactly the same isolated database.
export const environment = {
    ...process.env,
    APP_ENV: 'testing', APP_DEBUG: 'false', APP_URL: baseUrl,
    APP_KEY: 'base64:MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=',
    APP_CONFIG_CACHE: path.join(directory, 'config.php'),
    APP_ROUTES_CACHE: path.join(directory, 'routes.php'),
    DB_CONNECTION: 'sqlite', DB_DATABASE: path.join(directory, 'database.sqlite'), DB_URL: '',
    SESSION_DRIVER: 'database', SESSION_CONNECTION: 'sqlite', SESSION_COOKIE: 'cypress_session',
    SESSION_DOMAIN: 'null', SESSION_SECURE_COOKIE: 'false', CACHE_STORE: 'array',
    MAIL_MAILER: 'array', QUEUE_CONNECTION: 'sync', BCRYPT_ROUNDS: '4',
};
