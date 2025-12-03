import { Page } from '@playwright/test'

export async function loginTestUser(page: Page) {
  const username = 'testuser'
  const password = 'testpass123'

  await page.goto('/auth')
  await page.waitForLoadState('networkidle')

  // Try to register first (will fail if user exists, but that's ok)
  try {
    await page.getByLabel('Username').fill(username)
    await page.getByLabel('Password').fill(password)
    await page.getByRole('button', { name: /register/i }).click()
    await page.waitForSelector(
      'text=/Registered successfully|already exists|exists/i',
      { timeout: 5000 }
    )
  } catch {
    // User might already exist, continue to login
  }

  // Now login with the test user
  await page.goto('/auth')
  await page.waitForLoadState('networkidle')

  await page.getByLabel('Username').fill(username)
  await page.getByLabel('Password').fill(password)

  // Click login button
  await page.getByRole('button', { name: /login/i }).click()

  // Wait for success message
  await page.waitForSelector('text=/Login successful/i', { timeout: 10000 })

  // Wait for navigation to routes page
  await page.waitForURL('/routes', { timeout: 5000 })
}
