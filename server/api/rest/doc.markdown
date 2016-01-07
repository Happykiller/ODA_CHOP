# Routes

## QCM

* `/qcm/`
    * type : GET
    * optional params : "userId"
    * public : false
* `/qcm/`
    * type : POST
    * mandatory params : "userId","name","lang"
    * public : false
* `/qcm/search/file`
    * type : GET
    * public : false
* `/qcm/:name/:lang`
    * type : GET
    * public : true
    
## Session user

* `/sessionUser/`
    * type : POST
    * mandatory params : "firstName","lastName","qcmId","qcmName","qcmLang"
    * public : true
* `/sessionUser/record/`
    * type : POST
    * mandatory params : "question","nbErrors","sessionUserId"
    * public : true