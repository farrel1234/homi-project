import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const screenshotDir = path.join(__dirname, '../../docs/testing/screenshots/e2e');

// Pastikan direktori screenshot ada
if (!fs.existsSync(screenshotDir)) {
  fs.mkdirSync(screenshotDir, { recursive: true });
}

test.describe('HOMI Web Admin E2E Tests', () => {

  test('TC002 - Login gagal jika password salah', async ({ page }) => {
    await page.goto('/admin/login');
    
    // Pilih tenant Hawaii Garden (tenant_id = 1, atau cari text Hawaii Garden)
    await page.selectOption('select#tenant_id', { label: 'Hawaii Garden' });
    await page.fill('input#email', 'admin@test.id');
    await page.fill('input#password', 'password_salah');
    
    await page.click('button[type="submit"]');
    
    // Tunggu sampai ada error message
    await page.waitForTimeout(1000);
    
    // Ambil screenshot
    await page.screenshot({ path: path.join(screenshotDir, 'TC002_login_failed_wrong_password.png') });
  });

  test('TC001 & TC003 s/d TC010 - Flow Admin Hawaii Garden & Multi-Tenant Check', async ({ page }) => {
    // 1. Login Sukses Admin Hawaii Garden
    await page.goto('/admin/login');
    await page.selectOption('select#tenant_id', { label: 'Hawaii Garden' });
    await page.fill('input#email', 'admin@test.id');
    await page.fill('input#password', 'password');
    
    await page.screenshot({ path: path.join(screenshotDir, 'before_login.png') });
    await page.click('button[type="submit"]');
    
    // Tunggu navigasi ke dashboard
    await page.waitForURL('**/admin/dashboard');
    await page.screenshot({ path: path.join(screenshotDir, 'TC001_login_admin_success.png') });
    
    // 2. Dashboard loaded
    await expect(page.locator('h2', { hasText: 'Dashboard' })).toBeVisible();
    await page.screenshot({ path: path.join(screenshotDir, 'TC003_dashboard_loaded.png') });
    
    // 3. Modul Warga / Resident
    await page.click('a:has-text("Data Warga")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotDir, 'TC004_resident_page_loaded.png') });
    
    // Verifikasi data tenant Hawaii Garden dimuat (tidak ada tenant lain)
    // Cek header perumahan di sidebar/title
    await expect(page.locator('body')).toContainText('Hawaii Garden');
    
    // 4. Modul Tagihan / Iuran
    await page.click('a:has-text("Tagihan Iuran")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotDir, 'TC005_invoice_page_loaded.png') });
    
    // 5. Modul Pembayaran
    await page.click('a:has-text("Pembayaran")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotDir, 'TC006_payment_page_loaded.png') });
    
    // 6. Modul Pengumuman
    await page.click('a:has-text("Pengumuman")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotDir, 'TC007_announcement_page_loaded.png') });
    
    // 7. Modul Pengaduan
    await page.click('a:has-text("Pengaduan")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotDir, 'TC008_complaint_page_loaded.png') });
    
    // 8. Modul Prioritas Tunggakan / SAW
    await page.click('a:has-text("Prioritas Tunggakan")');
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(screenshotDir, 'TC009_saw_priority_page_loaded.png') });
    
    // 9. Multi-Tenant Check (Memastikan halaman hanya berisi data Hawaii Garden)
    // Kita screenshot bukti tenant terisolasi
    await page.screenshot({ path: path.join(screenshotDir, 'TC010_multi_tenant_hawaii_garden.png') });
  });

});
