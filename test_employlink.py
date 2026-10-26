
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

driver = webdriver.Chrome()

try:
    # Open your EmployLink website
    driver.get("http://127.0.0.1:5000/")

    # Wait for the page to load
    WebDriverWait(driver, 10).until(
        EC.presence_of_element_located((By.TAG_NAME, "body"))
    )

    # Check that the page has a title
    print("Page title:", driver.title)
    print("Current URL:", driver.current_url)

    # Check whether the page body is present
    body = driver.find_element(By.TAG_NAME, "body")
    assert body.is_displayed(), "Page body is not visible"

    print("Test Passed: EmployLink homepage loaded successfully.")

except Exception as e:
    print("Test Failed:", e)

finally:
    driver.quit()
