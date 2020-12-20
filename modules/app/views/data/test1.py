# -*- coding: utf-8 -*-
from underthesea import word_tokenize
import numpy as np
# import pandas as pd
# from sklearn.model_selection import train_test_split
# from sklearn.linear_model import LogisticRegression
# from sklearn.model_selection import GridSearchCV
# from sklearn.model_selection import cross_val_score
# import torch
# import transformers
# from transformers import BertModel, BertTokenizer
# from sklearn.externals import joblib

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

# df = pd.read_csv('data_crawler.csv', delimiter='\t', header=None)
# print(df.shape)
# # get all rows
# # print(df[0])

sentence = 'Chàng trai 9X Quảng Trị khởi nghiệp từ nấm sò'
sentence_1 = 'Hàng kém chất lượng!'
sentence_2 = 'Hàng chất lượng cao!'

words = word_tokenize(sentence)
print(words)

# '''
# Load pretrain model/ tokenizers
# '''
# model = BertModel.from_pretrained('bert-base-uncased')
# tokenizer = BertTokenizer.from_pretrained('bert-base-uncased')

# #encode lines
# tokenized = df[0].apply((lambda x: tokenizer.encode(x, add_special_tokens = True)))
# print('encode',tokenized[1])
# # decode
# print('decode',tokenizer.decode(tokenized[1]))
