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

import schedule
# import psycopg2

# import requests

def load_data_from_url(url):
    driver = webdriver.Chrome(executable_path=r'C:/Users/Ngoc Nghia/Downloads/chromedriver_win32/chromedriver.exe')  # Optional argument, if not specified will search path.
    print("Loading url: ", url)
    driver.get(url)
    list_review = []

    try:
        element = WebDriverWait(driver, 50).until(EC.presence_of_element_located((By.CSS_SELECTOR, ".media-chat-item.bg-indigo-400")))
    except:
        print('Do not have any comment.')

    reviews = driver.find_elements_by_css_selector('.media-chat-item.bg-indigo-400')
    
    for review in reviews:
        review = review.text
        if (review != "" or review.strip()):
            list_review.append(review)

    driver.close()
    return list_review

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
    print("Số bình luận xấu hoặc không thể hiện tính chất: ", bad)
    print("Số bình luận tốt: ", good)

    if good>bad:
        return "Tốt! Bạn nên ghé thăm!"
    else:
        return "Nên tìm hiểu trước khi đến!"

def processing_data(data):
    # 1. Standardize data
    data_frame = pd.DataFrame(data)
    print('Data frame:', data_frame)
    data_frame[0] = data_frame[0].apply(standardize_data)

    # 2. Tokenizer
    data_frame[0] = data_frame[0].apply(tokenizer)

    # 3. Embedding
    X_val = data_frame[0]
    return X_val

def load_pretrainModel(data):
    
    '''
    Load pretrain model/ tokenizers
    Return : features
    '''
    model = BertModel.from_pretrained('bert-base-uncased')
    tokenizer = BertTokenizer.from_pretrained('bert-base-uncased')

    #encode lines
    tokenized = data.apply((lambda x: tokenizer.encode(x, add_special_tokens = True)))

    # get lenght max of tokenized
    max_len = 0
    for i in tokenized.values:
        if len(i) > max_len:
            max_len = len(i)
    print('max len:', max_len)

    # if lenght of tokenized not equal max_len , so padding value 0
    padded = np.array([i + [0]*(max_len-len(i)) for i in tokenized.values])
    print('padded:', padded[1])
    print('len padded:', padded.shape)

    #get attention mask ( 0: not has word, 1: has word)
    attention_mask = np.where(padded ==0, 0,1)
    print('attention mask:', attention_mask[1])

    # Convert input to tensor
    padded = torch.tensor(padded, dtype = torch.long)
    attention_mask = torch.tensor(attention_mask, dtype = torch.long)

    # Load model
    with torch.no_grad():
        last_hidden_states = model(padded, attention_mask =attention_mask)
    #     print('last hidden states:', last_hidden_states)

    features = last_hidden_states[0][:,0,:].numpy()
    print('features:', features)
    
    return features

def predict(url):
    print(url)
    # url = format(str(sys.argv[1:][0]))
    # Load URL and print comments
    if url== "":
        url = "http://localhost/KLTN/web/app/place/detail/den-quan-thanh-tran-vu-quan"
    data = load_data_from_url(url)

    # Processing data (tokenize, regexp, ...)
    data = processing_data(data)

    # Load pretrain model BERT
    features = load_pretrainModel(data)

    # Load weights
    model = joblib.load('travel_model.pkl')

    # Result
    result = model.predict(features)
    print(result)
    print(analyze(result))

# def schedule:
#     try:
#         # connect to the PostgreSQL server
#         conn = psycopg2.connect(host="localhost",database="db_kltn", user="postgres", password="root")

#         # create a cursor
#         cur = conn.cursor()

#         # Execute a sql
#         names = cur.execute('SELECT name FROM place WHERE place_type_id='1'')

#         for names in name:
#             predict = predict('http://localhost/KLTN/web/app/place/detail/' + ''))
#             cur.execute(""" INSERT INTO interactive (rating) VALUES (%s)""", predict)

# schedule.every().day.at("10:30").do(schedule)
# while True:
#     schedule.run_pending()
#     time.sleep(1)