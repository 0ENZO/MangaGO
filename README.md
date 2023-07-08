# MangaGO

## Description
MangaGO is a project for professional and volunteer manga translators, allowing them to translate Japanese scans automatically. Text detection, retrieval, translation and replacement are fully automated processes. Once the translation is complete, the translator can validate or correct it.
This project has been developed as part of our end-of-year project, for our 5th year of IABD at ESGI.

## Demo
In the following video you'll see the possibilities offered by the MangaGO platform, from uploading scans, to the translation correction interface, to exporting translated scans. 

## Infrastructure
The project has been designed to work on the cloud, using a lot of AWS services (Lambda, ECS, RDS, S3,Sagemaker..)
Almost all the infrastructure is automatically deployed using Terraform.
Here is a diagram describing the implemented infrastructure :

## Models
To complete this project, we've used : 
- EasyOCR for text detection and coordinates extraction
- TesseractOCR for text retrieval 
- HuggingFace ["Helsinki-NLP/opus-mt-ja-fr"](https://huggingface.co/Helsinki-NLP/opus-mt-ja-fr) Transformers model, which was later finetuned 

## Dataset
In order to build up a dataset that would allow us to re-train our model, we used the annotations provided by the [Manga109](http://www.manga109.org/en/index.html) dataset.
All Japanese texts have been translated into French using DeepL API.

## Try-it yourself
We've made a notebook that aims to demonstrate the MangaGO solution in a concise way. You'll find it in /notebooks/mangago_concise_notebook.ipynb

## Contributors
This project have been made by :
- [FabienBarrios](https://github.com/FabienBarrios)
- [BilalMahjoubi](https://github.com/BilalMahjoubi)
- [0ENZO](https://github.com/0ENZO)

## Credits
```
@article{multimedia_aizawa_2020,
    author={Kiyoharu Aizawa and Azuma Fujimoto and Atsushi Otsubo and Toru Ogawa and Yusuke Matsui and Koki Tsubota and Hikaru Ikuta},
    title={Building a Manga Dataset ``Manga109'' with Annotations for Multimedia Applications},
    journal={IEEE MultiMedia},
    volume={27},
    number={2},
    pages={8--18},
    doi={10.1109/mmul.2020.2987895},
    year={2020}
}
```

## To do
- For the translation try JA->EN->FR rather JA->FR
- Improve error handling on the website
- Improve text remplacement method 
