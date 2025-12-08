import { test, expect } from '@playwright/test'

test.describe('Authentication', () => {
  test('should display login form', async ({ page }) => {
    await page.goto('/auth')
    await page.waitForLoadState('networkidle')

    await expect(page.getByText(/user registration.*login/i)).toBeVisible()
    await expect(page.getByLabel('Username')).toBeVisible()
    await expect(page.getByLabel('Password')).toBeVisible()
  })

  test('should register and login successfully', async ({ page }) => {
    const username = 'e2etestuser_' + Date.now()
    const password = 'testpass123'

    await page.goto('/auth')
    await page.waitForLoadState('networkidle')

    // Register
    await page.getByLabel('Username').fill(username)
    await page.getByLabel('Password').fill(password)
    await page.getByRole('button', { name: /register/i }).click()
    await expect(page.getByText(/Registered successfully/i)).toBeVisible({
      timeout: 10000,
    })

    // Login with the registered user
    await page.getByLabel('Username').fill(username)
    await page.getByLabel('Password').fill(password)
    await page.getByRole('button', { name: /login/i }).click()

    // Wait for navigation to routes page after successful login
    await page.waitForURL('/routes', { timeout: 10000 })
  })

  test('should show error with invalid credentials', async ({ page }) => {
    await page.goto('/auth')
    await page.waitForLoadState('networkidle')

    await page.getByLabel('Username').fill('wronguser')
    await page.getByLabel('Password').fill('wrongpass')

    await page.getByRole('button', { name: /login/i }).click()

    await expect(
      page.getByText(/Invalid credentials|Login failed/i)
    ).toBeVisible({ timeout: 10000 })
  })
})
