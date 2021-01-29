import time
import sys
import csv
from selenium import webdriver
from selenium.webdriver.common.by import By

url = format(str(sys.argv[1:][0]))

driver = webdriver.Chrome(executable_path=r'C:/Users/Ngoc Nghia/Downloads/chromedriver_win32/chromedriver.exe')  # Optional argument, if not specified will search path.
driver.get(url)

try:
    WebDriverWait(driver, 30).until(EC.visibility_of_all_elements_located(By.CSS_SELECTOR, 'span.more-string.lh-24'))
except:
    print('Do not have any comment.')

review_list = driver.find_elements_by_css_selector('span.more-string.lh-24')

with open('data_csv.csv', mode='a', newline='', encoding='utf-8') as review_file:
    review_writer = csv.writer(review_file, delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL)
    
    for review in review_list:
        more_content = review.find_elements(By.CSS_SELECTOR, '.morelink.fc-primary')
    
        if len(more_content) == 0:
            review = review.text
        else:
            more_content[0].click()
            review = review.text[:-8]

        if (review != "" or review.strip()):
            review_writer.writerow([review])

driver.close()