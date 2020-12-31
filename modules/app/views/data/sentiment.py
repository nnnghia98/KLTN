from underthesea import word_tokenize
import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import GridSearchCV
from sklearn.model_selection import cross_val_score
import torch
import transformers
from transformers import BertModel, BertTokenizer
from sklearn.externals import joblib
import csv

# Tokenizer
def tokenizer(row):
    return word_tokenize(row, format="text")

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

df = pd.read_csv('data_csv.csv', delimiter=',', header=None)
print(df.shape)
# get all rows
# print(df[0])

with open('data_csv.csv', encoding='utf-8') as csv_file:
    csv_reader = csv.reader(csv_file, delimiter=',')
    line_count = 0
    for row in csv_reader:
        # if line_count == 0:
        #     print(f'Column names are {", ".join(row)}')
        #     line_count += 1
        # else:
        #     print(f'\t{row[0]}')
        #     line_count += 1
        print(f'\t{row[0]}')
        line_count += 1        
    print(f'{line_count} comments.')

'''
Load pretrain model/ tokenizers
'''
model = BertModel.from_pretrained('bert-base-uncased')
tokenizer = BertTokenizer.from_pretrained('bert-base-uncased')

#encode lines
tokenized = df[0].apply((lambda x: tokenizer.encode(x, add_special_tokens = True)))
# print('encode', tokenized[1])
# decode
# print('decode', tokenizer.decode(tokenized[1]))

#get all label 
labels = np.zeros(len(df[0]))
for i in range(len(df[0])):
    print(labels[i])
    print(df[0][i])
    # labels[i] = df[0][i][-1]
print('labels shape:', labels.shape)

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
padded = torch.tensor(padded)
attention_mask = torch.tensor(attention_mask)


# Train model
with torch.no_grad():
    padded = torch.tensor(padded).to(torch.int64)
    last_hidden_states = model(padded, attention_mask = attention_mask)
#     print('last hidden states:', last_hidden_states)

features = last_hidden_states[0][:,0,:].numpy()
print('features:', features)

X_train, X_test, y_train, y_test = train_test_split(features, labels)

cl = LogisticRegression()
print(cl)
cl.fit(X_train, y_train)

# Save model
joblib.dump(cl, 'save_model.pkl')
sc = cl.score(X_test, y_test)
print('score:', sc)
