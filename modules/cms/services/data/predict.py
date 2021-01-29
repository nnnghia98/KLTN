import sys

import urllib.request
import re #regex
import csv
import os
import json
import pandas as pd
import urllib.request
import joblib #load, dump pkl
from underthesea import word_tokenize #word_tokenize of lines
import numpy as np
import transformers as ppb # load model BERT
from transformers import BertModel, BertTokenizer
import torch
from sklearn.model_selection import train_test_split
# scrap comment = selenium
from selenium import webdriver 
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
import time
# import requests

def load_data_from_url(url):
    driver = webdriver.Chrome(executable_path=r'C:/Users/Ngoc Nghia/Downloads/chromedriver_win32/chromedriver.exe')  # Optional argument, if not specified will search path.
    print("Loading url: ", url)
    driver.get(url)
    list_review = []

    try:
        WebDriverWait(driver, 30).until(EC.visibility_of_all_elements_located(By.CSS_SELECTOR, 'span.more-string.lh-24'))
    except:
        print('Do not have any comment.')

    review_list = driver.find_elements_by_css_selector('span.more-string.lh-24')
    
    for review in review_list:
        more_content = review.find_elements(By.CSS_SELECTOR, '.morelink.fc-primary')
    
        if len(more_content) == 0:
            review = review.text
        else:
            more_content[0].click()
            review = review.text[:-8]

        if (review != "" or review.strip()):
            list_review.append(review)

    driver.close()
    return list_review

def predict(url):
    print(url)
    # url = format(str(sys.argv[1:][0]))
    # 1. Load URL and print comments
    if url== "":
        url = "https://gody.vn/chau-a/viet-nam/da-nang/cau-tinh-yeu"
    data = load_data_from_url(url)
    print(data)
    # data = processing_data(data)
    # features = load_pretrainModel(data)
    # # 2. Load weights
    # model = joblib.load('save_model.pkl')
    # # 3. Result
    # result = model.predict(features)
    # print(result)
    # print(analyze(result))


def standardize_data(row):
    # remove stopword
    # Remove . ? , at index final
    row = re.sub(r"[\.,\?]+$-", "", row)
    # Remove all . , " ... in sentences
    row = row.replace(",", " ").replace(".", " ") \
        .replace(";", " ").replace("“", " ") \
        .replace(":", " ").replace("”", " ") \
        .replace('"', " ").replace("'", " ") \
        .replace("!", " ").replace("?", " ") \
        .replace("-", " ").replace("?", " ")

    row = row.strip()
    return row

# Tokenizer
def tokenizer(row):
    return word_tokenize(row, format="text")

def analyze(result):
    bad = np.count_nonzero(result)
    good = len(result) - bad
    print("No of bad and neutral comments = ", bad)
    print("No of good comments = ", good)

    if good>bad:
        return "Good! You can visit there"
    else:
        return "Bad! Please check it carefully!"

def processing_data(data):
    # 1. Standardize data
    data_frame = pd.DataFrame(data)
    print('data frame:', data_frame)
    data_frame[0] = data_frame[0].apply(standardize_data)

    # 2. Tokenizer
    data_frame[0] = data_frame[0].apply(tokenizer)

    # 3. Embedding
    X_val = data_frame[0]
    return X_val
