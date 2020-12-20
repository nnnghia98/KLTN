from vncorenlp import VnCoreNLP
import logging

annotator = VnCoreNLP(address="http://127.0.0.1", port=9000) 

# Input 
text = 'Đà Nẵng là một thành phố tuyệt đẹp, con người thân thiện, bầu không khí trong lành. Thực sự là một thành phố đáng sống!'

# To perform word segmentation, POS tagging, NER and then dependency parsing
annotated_text = annotator.annotate(text)   

# To perform word segmentation only
word_segmented_text = annotator.tokenize(text)

print(annotated_text)

# print(word_segmented_text)

# logging.info(annotated_text)
# input()