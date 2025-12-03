import { test, expect } from '@playwright/test'
import { loginTestUser } from './helpers/auth'

test.describe('Routes page', () => {
  test.beforeEach(async ({ page }) => {
    await loginTestUser(page)
  })

  test('should display route form', async ({ page }) => {
    await page.waitForLoadState('networkidle')
    await expect(page).toHaveURL('/routes')

    await expect(
      page.getByRole('heading', { name: /calculate route/i })
    ).toBeVisible()
    await expect(page.getByText('From Station')).toBeVisible()
    await expect(page.getByText('To Station')).toBeVisible()
  })

  test('should calculate route between two stations', async ({ page }) => {
    await page.waitForLoadState('networkidle')

    // Select from station (Montreux)
    await page.getByText('From Station').click()
    await page.getByText('Montreux', { exact: true }).click()

    // Select to station (Blonay)
    await page.getByText('To Station').click()
    await page.getByText('Blonay', { exact: true }).click()

    // Click calculate
    await page.getByRole('button', { name: /calculate/i }).click()

    // Wait for result
    await expect(page.getByText(/distance/i)).toBeVisible({ timeout: 10000 })
  })

  test('should show error for same station selection', async ({ page }) => {
    await page.waitForLoadState('networkidle')

    // Select same station
    await page.getByText('From Station').click()
    await page.getByText('Montreux', { exact: true }).click()

    await page.getByText('To Station').click()
    await page.getByText('Montreux', { exact: true }).click()

    await page.getByRole('button', { name: /calculate/i }).click()

    await expect(page.getByText(/same station/i)).toBeVisible({ timeout: 5000 })
  })
})
