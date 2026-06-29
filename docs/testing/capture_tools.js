import { chromium } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const logsDir = path.join(__dirname, 'logs');
const toolsDir = path.join(__dirname, 'screenshots/tools');

if (!fs.existsSync(toolsDir)) {
  fs.mkdirSync(toolsDir, { recursive: true });
}

function escapeHtml(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

async function captureSimulatedTerminal(title, command, logContent, targetFilename) {
  const htmlContent = `
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body {
    background-color: #0c0c0c;
    color: #cccccc;
    font-family: 'Consolas', 'Courier New', monospace;
    font-size: 14px;
    margin: 0;
    padding: 20px;
    line-height: 1.4;
  }
  .window {
    background-color: #0c0c0c;
    border: 1px solid #333333;
    border-radius: 6px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    overflow: hidden;
  }
  .title-bar {
    background-color: #202020;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #333333;
  }
  .dots {
    display: flex;
    gap: 6px;
    margin-right: 15px;
  }
  .dot {
    width: 12px;
    height: 12px;
    border-radius: 55%;
  }
  .dot.red { background-color: #ff5f56; }
  .dot.yellow { background-color: #ffbd2e; }
  .dot.green { background-color: #27c93f; }
  .title {
    color: #888888;
    font-size: 12px;
    flex-grow: 1;
    text-align: center;
    margin-right: 45px;
  }
  .terminal-body {
    padding: 15px;
    white-space: pre-wrap;
    word-break: break-all;
  }
  .prompt {
    color: #00ff00;
  }
  .command {
    color: #ffffff;
    font-weight: bold;
  }
  .ok-text {
    color: #27c93f;
    font-weight: bold;
  }
  .fail-text {
    color: #ff5f56;
    font-weight: bold;
  }
  .bold {
    font-weight: bold;
  }
</style>
</head>
<body>
  <div class="window">
    <div class="title-bar">
      <div class="dots">
        <div class="dot red"></div>
        <div class="dot yellow"></div>
        <div class="dot green"></div>
      </div>
      <div class="title">${title}</div>
    </div>
    <div class="terminal-body"><span class="prompt">PS C:\\laragon\\www\\homi-project&gt;</span> <span class="command">${command}</span>

${logContent}</div>
  </div>
</body>
</html>
  `;

  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  
  // Set viewport large enough
  await page.setViewportSize({ width: 900, height: 650 });
  await page.setContent(htmlContent);
  
  // Adjust viewport to content
  const contentHeight = await page.evaluate(() => document.body.scrollHeight);
  await page.setViewportSize({ width: 900, height: Math.max(contentHeight + 20, 500) });
  
  const destPath = path.join(toolsDir, targetFilename);
  await page.screenshot({ path: destPath, fullPage: true });
  console.log(`Saved screenshot to ${destPath}`);
  
  await browser.close();
}

async function run() {
  // 1. Playwright screenshot
  const pwLogPath = path.join(logsDir, 'playwright-result.txt');
  if (fs.existsSync(pwLogPath)) {
    const log = fs.readFileSync(pwLogPath, 'utf8');
    // Hilangkan karakter non-ASCII jika ada
    const cleanLog = escapeHtml(log);
    await captureSimulatedTerminal(
      "Playwright E2E Test Runner",
      "npx playwright test",
      cleanLog,
      "playwright_result.png"
    );
  }

  // 2. Newman API screenshot
  const apiLogPath = path.join(logsDir, 'api-test-result.txt');
  if (fs.existsSync(apiLogPath)) {
    const log = fs.readFileSync(apiLogPath, 'utf8');
    const cleanLog = escapeHtml(log);
    await captureSimulatedTerminal(
      "Newman API Test Collection",
      "npx newman run tests/api/HOMI_API_Testing.postman_collection.json -e tests/api/HOMI_API_Environment.postman_environment.json",
      cleanLog,
      "api_test_result.png"
    );
  }

  // 3. k6 Performance screenshot
  const k6LogPath = path.join(logsDir, 'k6-result.txt');
  if (fs.existsSync(k6LogPath)) {
    const log = fs.readFileSync(k6LogPath, 'utf8');
    const cleanLog = escapeHtml(log);
    await captureSimulatedTerminal(
      "k6 Performance Load Testing",
      "k6 run tests/performance/load-test.js",
      cleanLog,
      "k6_result.png"
    );
  }
}

run().catch(console.error);
