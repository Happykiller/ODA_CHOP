# Routes

## QCM

* `/qcm/`
    * type : GET
    * optional params : "userId"
    * public : false
* `/qcm/id`
    * type : GET
* `/qcm/`
    * type : POST
    * mandatory params : "userId","name","version","lang","date","desc","title","hours","duration","details","location"
    * public : false
* `/qcm/search/file/`
    * type : GET
    * public : false
* `/qcm/search/`
    * type : GET
    * mandatory params : "name","version","lang","date"
    * public : true
    
## Session user

* `/sessionUser/`
    * type : POST
    * mandatory params : "firstName","lastName","qcmId","qcmName","qcmLang","company"
    * public : true
* `/sessionUser/record/`
    * type : POST
    * mandatory params : "question","nbErrors","sessionUserId"
    * public : true  
* `/sessionUser/:userId`
    * type : GET
    * public : true
* `/sessionUser/:userId`
    * type : PUT
    * mandatory params : "state"
    * public : true
    
## Rapport
    
* `/rapport/qcm/:id/details/`
    * type : GET
* `/rapport/sessionUser/id:/record/`
    * type : GET
* `/rapport/sessionUser/:id/stats/`
    * type : GET
* `/rapport/emarg/:id`
    * type : GET