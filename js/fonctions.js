// Library of tools for the exemple
var g_lang = "EN";
var g_mode = 'read';
var g_scenarioCurrent = "";
var g_moduleCurrent = "";
var g_pageCurrent = "";
var g_nextSlide = null;
var g_prevSlide = null;
var g_modeEditPage = '';
var g_modeEditModule = '';

/**
 * @author FRO
 * @date 14/07/11
 */

(function() {
    'use strict';

    var
        /* version */
        VERSION = '0.1',
        
        _navBarEdit = [],
        
        _colorType = {
            "HTML" : "#FF8000",
            "TEXT" : "#08088A",
            "IMG" : "#01DF01",
            "CST" : "#FF0000",
            "NEW" : "#424242"
        }
        
    ;

    ////////////////////////// PRIVATE METHODS ////////////////////////

    /**
     * 
     * @returns {undefined}
     */
    function _init() {
        var tabSetting = {
        };
        
        var tabData = {
        };

        var retour = $.functionsLib.callRest(domaine+"phpsql/getEnv.php", tabSetting, tabData);
    }

    ////////////////////////// PUBLIC METHODS /////////////////////////

    $.functionsChop = {
        /* Version number */
        version: VERSION,

        /**
         * @name chargerSommaireScenario
         * @desc Hello
         * @p_param{string} param
         * @returns {boolean}
         */
        chargerSommaireScenario: function(p_params) {
            try {
                var tabSetting = {
                    type : "POST",
                    functionRetour : this.retourSommaireScenario
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/buildSomm.php", tabSetting, p_params);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.chargerSommaireScenario):" + er.message);
                return false;
            }
        },

        /**
         * @name retourSommaireScenario
         * @desc Hello
         * @p_param{string} param
         * @returns {boolean}
         */
        retourSommaireScenario: function(p_retour) {
            try {
                if(p_retour["strErreur"] == ""){
                    var params = {
                        lang : g_lang, 
                        mode : g_mode,
                        key : p_retour["data"]["resultat"]["titre_key"],
                        divContent : '[['+p_retour["data"]["resultat"]["titre_key"]+']]',
                        divId : 'span_sommaire_titre'
                    };
                    $.functionsChop.chargerElement(params);
                    
                    var params = {
                        lang : g_lang, 
                        mode : g_mode,
                        key : p_retour["data"]["resultat"]["resume_key"],
                        divContent : '[['+p_retour["data"]["resultat"]["resume_key"]+']]',
                        divId : 'div_sommaire_resume'
                    };
                    $.functionsChop.chargerElement(params);
                    
                    var strHtml = '';
                    
                    strHtml += "<div data-role=\"collapsible-set\" data-collapsed-icon=\"arrow-r\" data-expanded-icon=\"arrow-d\" data-corners=\"false\" id=\"lesommaire\">";
                    
                    var arrayModules = p_retour["data"]["resultat"]["modules"];
                    var ite = 0;
                    for(var indice in arrayModules){
                        ite++;
                        if(ite != 1){
                            strHtml += "</ul>";
                            strHtml += "</div>";
                        }
                        
                        if(g_mode == "edit"){
                            strHtml += "<div data-role=\"collapsible\">";
                            strHtml += '<h4>[['+arrayModules[indice]["titre_key"]+']] <a href="#" onclick="$.functionsChop.editModule({scenario:\''+g_scenarioCurrent+'\',module:\''+indice+'\',moduleBefore:\'\'});">Edit module</a></h4>';
                            strHtml += "<ul data-role=\"listview\">";
                        }else{
                            strHtml += "<div data-role=\"collapsible\">";
                            strHtml += '<h4>[['+arrayModules[indice]["titre_key"]+']]</h4>';
                            strHtml += "<ul data-role=\"listview\">";
                        }
                        
                        for(var indice0 in arrayModules[indice]["pages"]){
                            if(g_mode == "edit"){
                                strHtml += '<li ><a href="#" onclick="$.functionsChop.chargerPage({scenario:\''+g_scenarioCurrent+'\',module:\''+indice+'\',page:\''+indice0+'\'});">[['+arrayModules[indice]["pages"][indice0]["titre_key"]+']]</a><a href="#" onclick="$.functionsChop.editPage({scenario:\''+g_scenarioCurrent+'\',module:\''+indice+'\',pageBefore:\'\',page:\''+indice0+'\'});">Add module</a></li>';
                            }else{
                                strHtml += '<li ><a href="#" onclick="$.functionsChop.chargerPage({scenario:\''+g_scenarioCurrent+'\',module:\''+indice+'\',page:\''+indice0+'\'});">[['+arrayModules[indice]["pages"][indice0]["titre_key"]+']]</a></li>';
                            }
                        }
                        
                        if(g_mode == "edit"){
                            strHtml += '<li ><a href="#" onclick="$.functionsChop.editPage({scenario:\''+g_scenarioCurrent+'\',module:\''+indice+'\',pageBefore:\''+indice0+'\',page:\'\'});">Add page</a></li>';
                        }
                    }
                    strHtml += "</ul>";
                    strHtml += "</div>";
                    
                    if(g_mode == "edit"){
                        strHtml += "<div data-role=\"collapsible\">";
                        strHtml += '<h4><a href="#" onclick="$.functionsChop.editModule({scenario:\''+g_scenarioCurrent+'\',module:\'\',moduleBefore:\''+indice+'\'});">Add module</a></h4>';
                        strHtml += "<ul data-role=\"listview\">";
                        strHtml += "</ul>";
                        strHtml += "</div>";
                    }
                    
                    strHtml += "</div>";
                    
                    var params = {
                        lang : g_lang, 
                        mode : g_mode,
                        key : '',
                        divContent : strHtml,
                        divId : 'div_sommaire_content'
                    };
                    $.functionsChop.chargerElement(params);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.retourSommaireScenario):" + er.message);
                return false;
            }
        },

        /**
         * @name chargerPage
         * @desc Hello
         * @p_param{string} param
         * @returns {boolean}
         */
        chargerPage: function(p_params) {
            try {
                g_scenarioCurrent = p_params.scenario;
                g_moduleCurrent = p_params.module;
                g_pageCurrent = p_params.page;
                
                var tabParams = {
                    module : g_moduleCurrent,
                    page : g_pageCurrent
                };
                
                var tabSetting = {
                    type : "POST",
                    functionRetour : this.retourChargerPage
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/chargePage.php", tabSetting, tabParams);
                
                //send msg interco
                var params = {
                    lang:null
                    ,scenario:g_scenarioCurrent
                    ,module:g_moduleCurrent
                    ,page:g_pageCurrent
                    ,milis:$.functionsLib.getMilise()
                };
                $.functionsStorage.set('INTERCOM_CHOP_MSG-TO-PRESS',params,60);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.chargerPage):" + er.message);
                return false;
            }
        },

        /**
         * @name retourChargerPage
         * @desc Hello
         * @p_param{string} param
         * @returns {boolean}
         */
        retourChargerPage: function(p_retour) {
            try {
                if(p_retour["strErreur"] == ""){
                    var params = {
                        lang : g_lang, 
                        mode : g_mode,
                        key : p_retour["data"]["resultat"]["titre_key"],
                        divContent : '[['+p_retour["data"]["resultat"]["titre_key"]+']]',
                        divId : 'span_titre'
                    };
                    $.functionsChop.chargerElement(params);

                    var params = {
                        lang : g_lang, 
                        mode : g_mode,
                        key : p_retour["data"]["resultat"]["content_key"],
                        divContent : '[['+p_retour["data"]["resultat"]["content_key"]+']]',
                        divId : 'div_content'
                    };
                    $.functionsChop.chargerElement(params);

                    var params = {
                        lang : g_lang, 
                        mode : g_mode,
                        key : p_retour["data"]["resultat"]["tips_key"],
                        divContent : '<hr>[['+p_retour["data"]["resultat"]["tips_key"]+']]',
                        divId : 'div_tips'
                    };
                    $.functionsChop.chargerElement(params);
                    
                    var before = p_retour["data"]["navigationBefore"]["data"];
                    var titre = "";
                    var page = "";
                    for(var indice in before){
                        var titre = before[indice]["titre"];
                        var page = before[indice]["beforePage"];
                    }
                    
                    if((titre != "")&&(page != "")){
                        var params = {
                            lang : g_lang, 
                            mode : g_mode,
                            key : '',
                            divContent : '<button class="ui-mini ui-btn ui-btn-inline ui-icon-arrow-l ui-btn-icon-left" onclick="$.functionsChop.chargerPage({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\',page:\''+page+'\'});" id="bt_navBefore">[['+titre+']]</button>',
                            divId : 'leftNav'
                        };
                        $.functionsChop.chargerElement(params);
                        if(typeof g_prevSlide != "undefined"){g_prevSlide = {"scenario":g_scenarioCurrent, "module":g_moduleCurrent, "page":page};}
                    }else{
                        $('#leftNav').html('');
                        if(typeof g_prevSlide != "undefined"){g_prevSlide = null;}
                    }
                    
                    var after = p_retour["data"]["navigationAfter"]["data"];
                    var titre = "";
                    var page = "";
                    for(var indice in after){
                        var titre = after[indice]["titre"];
                        var page = after[indice]["afterPage"];
                    }
                    
                    if((titre != "")&&(page != "")){
                        var params = {
                            lang : g_lang, 
                            mode : g_mode,
                            key : '',
                            divContent : '<button class="ui-mini ui-btn ui-btn-inline ui-icon-arrow-r ui-btn-icon-right" onclick="$.functionsChop.chargerPage({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\',page:\''+page+'\'});" id="bt_navAfter">[['+titre+']]</button>',
                            divId : 'rightNav'
                        };
                        $.functionsChop.chargerElement(params);
                        if(typeof g_nextSlide != "undefined"){g_nextSlide = {"scenario":g_scenarioCurrent, "module":g_moduleCurrent, "page":page};}
                    }else{
                        $('#rightNav').html('');
                        if(typeof g_nextSlide != "undefined"){g_nextSlide = null;}
                    }
                    
                    $('#content_page').show();
                } else{
                    $.functionsLib.notification("Erreur : "+p_retour["strErreur"], $.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.retourChargerPage):" + er.message);
                return false;
            }
        },

        /**
         * @name chargerElement
         * @desc Hello
         * @p_param{string} param
         * @returns {boolean}
         */
        chargerElement: function(p_params) {
            try {
                var tabSetting = {
                    type : "POST",
                    functionRetour : this.retourChargerElement
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/changeDiv.php", tabSetting, p_params);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.chargerElement):" + er.message);
                return false;
            }
        },

        /**
         * @name retourChargerElement
         * @desc Hello
         * @p_param{string} param
         * @returns {boolean}
         */
        retourChargerElement: function(p_retour) {
            try {
                if(p_retour["strErreur"] == ""){
                    var myDivId = p_retour["data"]["divId"];
                    var myDivContent = p_retour["data"]["divContent"];
                    $('#'+myDivId).html(myDivContent).trigger('create');
                } else{
                    $.functionsLib.notification("Erreur : "+p_retour["strErreur"], $.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.retourChargerElement):" + er.message);
                return false;
            }
        },
        
        /**
         * @name editElement
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        editElement: function(p_params) {
            try {
                if(p_params.type == "NEW"){
                    $.functionsChopView.editElementNew(p_params);
                }else{
                    $.functionsChopView.editElementExist(p_params);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.editElement):" + er.message);
                return false;
            }
        },
        
        openTabPreview: function() {
            try {
                $('#divPopupEdit').hide();
                $('#divPopupPreview').show();
                
                var params = {
                    lang : g_lang, 
                    mode : 'read',
                    key : '',
                    divContent : $('#inputPopupCode').val(),
                    divId : 'divPopupPreview'
                };
                $.functionsChop.chargerElement(params);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.openTabPreview):" + er.message);
                return false;
            }
        },
        
        openPress : function(p_scenario, p_module, p_page, p_openMod) {
            try {
                if(p_module == ""){p_module = g_moduleCurrent;}
                if(p_page == ""){p_page = g_pageCurrent;}
                
                if(p_page != ""){
                    var lang = $('#select-lang_'+p_scenario).val();
                    if($.functionsLib.isUndefined(lang)){
                        lang = $('#select-lang').val();
                    }

                    var params = {
                        lang:lang
                        ,scenario:p_scenario
                        ,module:p_module
                        ,page:p_page
                        ,milis:$.functionsLib.getMilise()
                    };
                    $.functionsStorage.set('INTERCOM_CHOP_MSG-TO-PRESS',params,60);

                    var datas = $.functionsStorage.get('INTERCOM_CHOP_MSG-PRESS-ALIVE');
                    if(datas == null){
                        window.open("./page_press.html");
                    }

                    if(p_openMod){
                        window.location=('./page_mod.html?scenarioKey='+p_scenario+'&lang='+lang+'&mili='+$.functionsLib.getMilise());
                    }
                }else{
                    $.functionsLib.notification("Warning seleted a page.", $.functionsLib.oda_msg_color.WARNING);
                }
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.openPress):" + er.message);
            }
        },
        
        openTabCode: function(p_type) {
            try {
                //TODO pb si retour pas assez rapide de inputPopupCode, il faudrait charger aprés le retour
                if((p_type == 'HTML') || (p_type == 'TEXT')){
                    $('#inputPopupCode').ckeditor();
                }
                
                $('#divPopupPreview').hide();
                $('#divPopupEdit').show();
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.openTabCode):" + er.message);
                return false;
            }
        },
        
        sauvElement: function(p_params) {
            try {
                var key = p_params.key;
                var lang = p_params.lang;
                var userId = $.functionsLib.getUserInfo().id;
                var content = $('#inputPopupCode').val();
                 
                var tabSetting = {
                    type : "POST"
                };
                
                var params = {
                    key : key, 
                    lang : lang,
                    userId : userId,
                    content : content
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/saveElement.php", tabSetting, params);
                
                if(retour["data"]["id"] == null){
                    $('#divPopupPreview').hide();
                    $('#divPopupEdit').hide();
                    $('#divPopupNewElement').show();
                }else{
                    var element = this.getElement({key : p_params.key});
                    $.functionsChop.editElement({key:element["key"], lang:lang, type:element['type'], previous:'escape'});
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.sauvElement):" + er.message);
                return false;
            }
        },
        
        createElement: function(p_params) {
            try {
                var key = p_params.key;
                var userId = $.functionsLib.getUserInfo().id;
                var type = $('#newElementType').val();
                var description = $('#newElementDescription').val();
                 
                var tabSetting = {
                    type : "POST"
                };
                
                var params = {
                    key : key, 
                    userId : userId,
                    type : type,
                    description : description
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/createElement.php", tabSetting, params);
                
                if(retour["data"]["strErreur"] != ''){
                    $.functionsChop.editElement({key:key, type:type, previous:'escape'});
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.createElement.createElement):" + er.message);
                return false;
            }
        },
        
        chargerListScenario: function(p_params) {
            try {
                var tabSetting = {
                    functionRetour : this.retourListScenario
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/getListScenario.php", tabSetting, p_params);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.createElement.chargerListScenario):" + er.message);
                return false;
            }
        },

        /**
         * @name retourListScenario
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        retourListScenario: function(p_retour) {
            try {
                if(p_retour["strErreur"] == ""){
                    var langs = $.functionsChop.getLangs();
                    
                    var strHtml = "";
                    var scenarios = p_retour["data"]["resultat"]["data"];
                    for(var indice in scenarios){
                        if($.functionsLib.getUserInfo().profile <= 30){
                            strHtml += '<div data-role="controlgroup" data-type="horizontal" data-mini="true">';
                            strHtml += '<a href="#" onclick="$.functionsChop.openModScenario({scenario:\''+scenarios[indice]['key']+'\'});" class="ui-btn ui-corner-all ui-icon-eye ui-btn-icon-right">'+scenarios[indice]['key']+'</a>';
                            strHtml += '<select name="select-lang_'+scenarios[indice]['key']+'" id="select-lang_'+scenarios[indice]['key']+'">';

                            for(var indiceLangs in langs){
                                strHtml += '<option value="'+langs[indiceLangs]["code"]+'">'+langs[indiceLangs]["langue"]+'</option>';
                            }   

                            strHtml += '</select>';
                            if($.functionsLib.getUserInfo().profile <= 20){
                                strHtml += '<a href="#" onclick="$.functionsChop.openPress(\''+scenarios[indice]['key']+'\',\''+scenarios[indice]['module_start_key']+'\',\''+scenarios[indice]['page_start_key']+'\', true);" class="ui-btn ui-corner-all ui-icon-clock ui-btn-icon-right">Presentation</a>';
                            }
                            strHtml += '<a href="#" onclick="$.functionsChop.openPdfScenario(\''+scenarios[indice]['key']+'\')" class="ui-btn ui-corner-all ui-icon-action ui-btn-icon-right">PDF</a>';
                            if(g_mode == 'edit'){
                                strHtml += '<a href="#" onclick="$.functionsChop.editScenario({mode:\'edit\', scenario:\''+scenarios[indice]['key']+'\'});" class="ui-btn ui-corner-all ui-icon-edit ui-btn-icon-right">Modif</a>';
                                strHtml += '<a href="#" onclick="$.functionsChop.removeScenario({scenario:\''+scenarios[indice]['key']+'\'});" class="ui-btn ui-corner-all ui-icon-delete ui-btn-icon-right">Delete</a>';
                            }
                            strHtml += '</div><br>';
                        }else{
                            strHtml += '<a href="#" class="ui-btn ui-mini ui-corner-all ui-btn-inline">'+scenarios[indice]['key']+'</a><br>';
                        }
                    }
                    if(g_mode == 'edit'){
                        strHtml += '<a href="#" onclick="$.functionsChop.editScenario({mode:\'new\'});" class="ui-btn ui-btn-inline ui-corner-all ui-icon-plus ui-btn-icon-right ui-mini">Add</a>';
                    }
                    $('#div_content').html(strHtml).trigger('create');
                } else{
                    $.functionsLib.notification("Erreur : "+p_retour["strErreur"], $.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.retourListScenario):" + er.message);
                return false;
            }
        },

        /**
         * @name openModScenario
         * @desc Hello
         * @pparam {objet} p_params
         * @returns {boolean}
         */
        openModScenario: function(p_params) {
            try {
                var scenario = p_params.scenario;
                var lang = $('#select-lang_'+scenario).val();
                
                window.location = "./page_mod.html?scenarioKey="+scenario+"&lang="+lang+"&mili="+$.functionsLib.getMilise();
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.openModScenario):" + er.message);
                return false;
            }
        },

        /**
         * @name openPdfScenario
         * @desc Hello
         * @pparam {string} p_scenario
         * @returns {boolean}
         */
        openPdfScenario: function(p_scenario) {
            try {
                var lang = $('#select-lang_'+p_scenario).val();
                
                window.open("./phpsql/getScenarioInPdf.php?milis="+$.functionsLib.getMilise()+"&keyScenar="+p_scenario+"&lang="+lang);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.openPdfScenario):" + er.message);
                return false;
            }
        },
        
        /**
         * @name getLangs
         * @desc Hello
         * @returns {json}
         */
        getLangs : function() {
            try {
                var tabSetting = {
                };
                
                var tabParams = {
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/getLangs.php", tabSetting, tabParams);
                
                var recordLangs = retour["data"]["resultat"]["data"];
                
                return recordLangs;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.getLangs):" + er.message);
                return null;
            }
        },
        
        /**
         * @name getElement
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        getElement : function(p_param) {
            try {
                var tabSetting = {
                };
                
                var tabParams = {
                    eltKey : p_param.key
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/getElement.php", tabSetting, tabParams);
                
                var recordElement = retour["data"]["resultat"];
                
                return recordElement;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.getElement):" + er.message);
                return null;
            }
        },
        
        /**
         * @name newPage
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        editPage : function(p_params) {
            try {
                g_scenarioCurrent = p_params.scenario;
                g_moduleCurrent = p_params.module;
                g_pageCurrent = p_params.page;
                var pageBefore = p_params.pageBefore;
                
                if(g_pageCurrent != ''){
                    g_modeEditPage = 'edit';
                    
                    var tabSetting = {
                    };
                    
                    var tabParams = {
                        scenarioCurrent : g_scenarioCurrent,
                        moduleCurrent : g_moduleCurrent,
                        pageCurrent : g_pageCurrent
                    };

                    var retourMapping = $.functionsLib.callRest(domaine+"phpsql/getMappingPage.php", tabSetting, tabParams);

                    var tabParams = {
                        pageKey : g_pageCurrent
                    };

                    var retourPage = $.functionsLib.callRest(domaine+"phpsql/getPageDetails.php", tabSetting, tabParams);
                    
                    var strhtml = "";
                    strhtml += '<h2 id="pageDefinition">Page Definition</h2>';
                    strhtml += '<label for="input_pageKey">Page key:</label>';
                    strhtml += '<input disabled="disabled" type="text" id="input_pageKey" value="'+g_pageCurrent+'" data-clear-btn="true" placeholder="Upper, no space" ui-mini>';
                    strhtml += '<label for="input_pageDescription">Page description:</label>';
                    strhtml += '<input type="text" id="input_pageDescription" value="'+retourPage["data"]["resultat"]["description"]+'" data-clear-btn="true" placeholder="Description" ui-mini>';
                    strhtml += '<label for="input_pageTitre">Item titre:</label>';
                    strhtml += '<input type="text" id="input_pageTitre" value="'+retourPage["data"]["resultat"]["titre_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_pageContent">Item content:</label>';
                    strhtml += '<input type="text" id="input_pageContent" value="'+retourPage["data"]["resultat"]["content_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_pageTips">Item tips:</label>';
                    strhtml += '<input type="text" id="input_pageTips" value="'+retourPage["data"]["resultat"]["tips_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<h2 id="pageDefinition">Page implementation</h2>';
                    strhtml += '<table style="wight:100%"><tr>';
                    strhtml += '<td><label for="input_pageBefore">Page before:</label>';
                    strhtml += '<input type="text" id="input_pageBefore" value="'+retourMapping["data"]["resultat"]["pageBefore"]+'" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td><label for="input_pageAfter">Page after:</label>';
                    strhtml += '<input type="text" id="input_pageAfter" value="'+retourMapping["data"]["resultat"]["pageAfter"]+'" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td><a href="#" onclick="$.functionsChop.removePageModule({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\',page:\''+g_pageCurrent+'\'});" class="ui-btn ui-icon-delete ui-corner-all ui-mini">Remove from mapping</a></td>';
                    strhtml += '</table></tr>';
                    strhtml += '<div data-role="controlgroup" data-type="horizontal" data-mini="true">';
                    strhtml += '<a href="#" onclick="$.functionsChop.submitEditPage({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\'});" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Submit</a>';
                    strhtml += '<a href="#" onclick="$(\'#popup\').popup(\'close\');" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Cancel</a>';
                    strhtml += '</div>';
                    
                    $.functionsLib.affichePopUp('Edit page '+g_pageCurrent+' for the module : '+g_moduleCurrent+' in scenario : '+g_scenarioCurrent, strhtml);
                }else{
                    g_modeEditPage = 'new';
                    
                    var strhtml = "";
                    strhtml += '<h2 id="pageDefinition">Page Definition</h2>';
                    strhtml += '<label for="input_pageKey">Page key:</label>';
                    strhtml += '<input type="text" id="input_pageKey" value="" data-clear-btn="true" placeholder="Upper, no space" ui-mini>';
                    strhtml += '<label for="input_pageDescription">Page description:</label>';
                    strhtml += '<input type="text" id="input_pageDescription" value="" data-clear-btn="true" placeholder="Description" ui-mini>';
                    strhtml += '<label for="input_pageTitre">Item titre:</label>';
                    strhtml += '<input type="text" id="input_pageTitre" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_pageContent">Item content:</label>';
                    strhtml += '<input type="text" id="input_pageContent" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_pageTips">Item tips:</label>';
                    strhtml += '<input type="text" id="input_pageTips" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<h2 id="pageDefinition">Page implementation</h2>';
                    strhtml += '<table style="wight:100%"><tr>';
                    strhtml += '<td><label for="input_pageBefore">Page before:</label>';
                    strhtml += '<input type="text" id="input_pageBefore" value="'+pageBefore+'" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td><label for="input_pageAfter">Page after:</label>';
                    strhtml += '<input type="text" id="input_pageAfter" value="" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td></td>';
                    strhtml += '</table></tr>';
                    strhtml += '<div data-role="controlgroup" data-type="horizontal" data-mini="true">';
                    strhtml += '<a href="#" onclick="$.functionsChop.submitEditPage({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\'});" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Submit</a>';
                    strhtml += '<a href="#" onclick="$(\'#popup\').popup(\'close\');" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Cancel</a>';
                    strhtml += '</div>';
                    
                    $.functionsLib.affichePopUp('New page for the module : '+g_moduleCurrent+' in scenario : '+g_scenarioCurrent, strhtml);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.editPage):" + er.message);
                return null;
            }
        },
        
        /**
         * @name removePageModule
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        removePageModule : function(p_params) {
            try {
                var tabSetting = {
                    type : "POST"
                };
                
                var tabParams = {
                    module : p_params.module,
                    page : p_params.page
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/removePageModule.php", tabSetting, tabParams);
                
                if(retour["strErreur"] == ""){
                    var params = {
                        keyScenar : p_params.scenario
                    };
                    $.functionsChop.chargerSommaireScenario(params);
                    
                    $('#popup').popup('close');
                }else{
                    $.functionsLib.notification(retour["strErreur"],$.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.removePageModule):" + er.message);
                return null;
            }
        },
        
        /**
         * @name submitEditPage
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        submitEditPage : function(p_params) {
            try {
                var input_pageKey = $('#input_pageKey').val();
                var input_pageDescription = $('#input_pageDescription').val();
                var input_pageTitre = $('#input_pageTitre').val();
                var input_pageContent = $('#input_pageContent').val();
                var input_pageTips = $('#input_pageTips').val();
                var input_pageBefore = $('#input_pageBefore').val();
                var input_pageAfter = $('#input_pageAfter').val();
                
                var tabSetting = {
                    type : "POST"
                };
                
                var tabParams = {
                    input_pageKey : input_pageKey,
                    input_pageDescription : input_pageDescription,
                    input_pageTitre : input_pageTitre,
                    input_pageContent : input_pageContent,
                    input_pageTips : input_pageTips,
                    input_pageBefore : input_pageBefore,
                    input_pageAfter : input_pageAfter,
                    input_moduleKey : p_params.module,
                    input_mode : g_modeEditPage
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/editPage.php", tabSetting, tabParams);
                
                if(retour["strErreur"] == ""){
                    var params = {
                        keyScenar : p_params.scenario
                    };
                    $.functionsChop.chargerSommaireScenario(params);
                    
                    $('#popup').popup('close');
                }else{
                    $.functionsLib.notification(retour["strErreur"],$.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.submitEditPage):" + er.message);
                return null;
            }
        },
        
        /**
         * @name editModule
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        editModule : function(p_params) {
            try {
                g_scenarioCurrent = p_params.scenario;
                g_moduleCurrent = p_params.module;
                var moduleBefore = p_params.moduleBefore;
                
                if(g_moduleCurrent != ''){
                    g_modeEditModule = 'edit';
                    
                    var tabSetting = {
                    };
                    
                    var tabParams = {
                        scenarioCurrent : g_scenarioCurrent,
                        moduleCurrent : g_moduleCurrent
                    };

                    var retourMapping = $.functionsLib.callRest(domaine+"phpsql/getMappingModule.php", tabSetting, tabParams);

                    var tabParams = {
                        moduleKey : g_moduleCurrent
                    };

                    var retourModule = $.functionsLib.callRest(domaine+"phpsql/getModuleDetails.php", tabSetting, tabParams);
                    
                    var strhtml = "";
                    strhtml += '<h2 id="moduleDefinition">Module Definition</h2>';
                    strhtml += '<label for="input_moduleKey">Module key:</label>';
                    strhtml += '<input disabled="disabled" type="text" id="input_moduleKey" value="'+g_moduleCurrent+'" data-clear-btn="true" placeholder="Upper, no space" ui-mini>';
                    strhtml += '<label for="input_moduleDescription">Module description:</label>';
                    strhtml += '<input type="text" id="input_moduleDescription" value="'+retourModule["data"]["resultat"]["data"]["description"]+'" data-clear-btn="true" placeholder="Description" ui-mini>';
                    strhtml += '<label for="input_moduleTitre">Item titre:</label>';
                    strhtml += '<input type="text" id="input_moduleTitre" value="'+retourModule["data"]["resultat"]["data"]["titre_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_moduleContent">Item resume:</label>';
                    strhtml += '<input type="text" id="input_moduleResume" value="'+retourModule["data"]["resultat"]["data"]["resume_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_moduleTips">Item start page:</label>';
                    strhtml += '<input type="text" id="input_moduleStartPage" value="'+retourModule["data"]["resultat"]["data"]["startPage_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<h2 id="moduleDefinition">Module implementation</h2>';
                    strhtml += '<table style="wight:100%"><tr>';
                    strhtml += '<td><label for="input_moduleBefore">Module before:</label>';
                    strhtml += '<input type="text" id="input_moduleBefore" value="'+retourMapping["data"]["resultat"]["data"]["moduleBefore"]+'" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td><label for="input_moduleAfter">Module after:</label>';
                    strhtml += '<input type="text" id="input_moduleAfter" value="'+retourMapping["data"]["resultat"]["data"]["moduleAfter"]+'" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td><a href="#" onclick="$.functionsChop.removeModuleModule({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\'});" class="ui-btn ui-icon-delete ui-corner-all ui-mini">Remove from mapping</a></td>';
                    strhtml += '</table></tr>';
                    strhtml += '<div data-role="controlgroup" data-type="horizontal" data-mini="true">';
                    strhtml += '<a href="#" onclick="$.functionsChop.submitEditModule({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\'});" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Submit</a>';
                    strhtml += '<a href="#" onclick="$(\'#popup\').popup(\'close\');" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Cancel</a>';
                    strhtml += '</div>';
                    
                    $.functionsLib.affichePopUp('Edit module : '+g_moduleCurrent+' in scenario : '+g_scenarioCurrent, strhtml);
                }else{
                    g_modeEditModule = 'new';
                    
                    var strhtml = "";
                    strhtml += '<h2 id="moduleDefinition">Module Definition</h2>';
                    strhtml += '<label for="input_moduleKey">Module key:</label>';
                    strhtml += '<input type="text" id="input_moduleKey" value="" data-clear-btn="true" placeholder="Upper, no space" ui-mini>';
                    strhtml += '<label for="input_moduleDescription">Module description:</label>';
                    strhtml += '<input type="text" id="input_moduleDescription" value="" data-clear-btn="true" placeholder="Description" ui-mini>';
                    strhtml += '<label for="input_moduleTitre">Item titre:</label>';
                    strhtml += '<input type="text" id="input_moduleTitre" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_moduleContent">Item resume:</label>';
                    strhtml += '<input type="text" id="input_moduleResume" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_moduleTips">Item start page:</label>';
                    strhtml += '<input type="text" id="input_moduleStartPage" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<h2 id="moduleDefinition">Module implementation</h2>';
                    strhtml += '<table style="wight:100%"><tr>';
                    strhtml += '<td><label for="input_moduleBefore">Module before:</label>';
                    strhtml += '<input type="text" id="input_moduleBefore" value="'+moduleBefore+'" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td><label for="input_moduleAfter">Module after:</label>';
                    strhtml += '<input type="text" id="input_moduleAfter" value="" data-clear-btn="true" placeholder="Empty for auto design" ui-mini></td>';
                    strhtml += '<td></td>';
                    strhtml += '</table></tr>';
                    strhtml += '<div data-role="controlgroup" data-type="horizontal" data-mini="true">';
                    strhtml += '<a href="#" onclick="$.functionsChop.submitEditModule({scenario:\''+g_scenarioCurrent+'\',module:\''+g_moduleCurrent+'\'});" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Submit</a>';
                    strhtml += '<a href="#" onclick="$(\'#popup\').popup(\'close\');" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Cancel</a>';
                    strhtml += '</div>';
                    
                    $.functionsLib.affichePopUp('New module in scenario : '+g_scenarioCurrent, strhtml);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.editModule):" + er.message);
                return null;
            }
        },
        
        /**
         * @name submitEditModule
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        submitEditModule : function(p_params) {
            try {
                var input_moduleKey = $('#input_moduleKey').val();
                var input_moduleDescription = $('#input_moduleDescription').val();
                var input_moduleTitre = $('#input_moduleTitre').val();
                var input_moduleResume = $('#input_moduleResume').val();
                var input_moduleStartPage = $('#input_moduleStartPage').val();
                var input_moduleBefore = $('#input_moduleBefore').val();
                var input_moduleAfter = $('#input_moduleAfter').val();
                
                var tabSetting = {
                    type : "POST"
                };
                
                var tabParams = {
                    input_moduleKey : input_moduleKey,
                    input_moduleDescription : input_moduleDescription,
                    input_moduleTitre : input_moduleTitre,
                    input_moduleResume : input_moduleResume,
                    input_moduleStartPage : input_moduleStartPage,
                    input_moduleBefore : input_moduleBefore,
                    input_moduleAfter : input_moduleAfter,
                    input_scenarioKey : p_params.scenario,
                    input_mode : g_modeEditModule
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/editModule.php", tabSetting, tabParams);
                
                if(retour["strErreur"] == ""){
                    var params = {
                        keyScenar : p_params.scenario
                    };
                    $.functionsChop.chargerSommaireScenario(params);
                    
                    $('#popup').popup('close');
                }else{
                    $.functionsLib.notification(retour["strErreur"],$.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.submitEditModule):" + er.message);
                return null;
            }
        },
        
        /**
         * @name editScenario
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        editScenario : function(p_params) {
            try {
                if(p_params.mode == 'edit'){
                    var tabSetting = {
                    };

                    var tabParams = {
                        scenarioKey : p_params.scenario
                    };

                    var retourScenario = $.functionsLib.callRest(domaine+"phpsql/getScenarioDetails.php", tabSetting, tabParams);
                    
                    var strhtml = "";
                    strhtml += '<label for="input_scenarioKey">Scenario key:</label>';
                    strhtml += '<input disabled="disabled" type="text" id="input_scenarioKey" value="'+p_params.scenario+'" data-clear-btn="true" placeholder="Upper, no space" ui-mini>';
                    strhtml += '<label for="input_scenarioDescription">Page description:</label>';
                    strhtml += '<input type="text" id="input_scenarioDescription" value="'+retourScenario["data"]["resultat"]["description"]+'" data-clear-btn="true" placeholder="Description" ui-mini>';
                    strhtml += '<label for="input_scenarioTitre">Item titre:</label>';
                    strhtml += '<input type="text" id="input_scenarioTitre" value="'+retourScenario["data"]["resultat"]["titre_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_scenarioResume">Item resume:</label>';
                    strhtml += '<input type="text" id="input_scenarioResume" value="'+retourScenario["data"]["resultat"]["resume_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_scenarioStartModule">Item start module :</label>';
                    strhtml += '<input type="text" id="input_scenarioStartModule" value="'+retourScenario["data"]["resultat"]["data"]["startModule_key"]+'" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<div data-role="controlgroup" data-type="horizontal" data-mini="true">';
                    strhtml += '<a href="#" onclick="$.functionsChop.submitEditScenario({scenario:\''+p_params.scenario+'\'});" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Submit</a>';
                    strhtml += '<a href="#" onclick="$(\'#popup\').popup(\'close\');" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Cancel</a>';
                    strhtml += '</div>';
                    
                    $.functionsLib.affichePopUp('Edit in scenario : '+p_params.scenario, strhtml);
                }else{
                    var strhtml = "";
                    strhtml += '<label for="input_scenarioKey">Scenario key:</label>';
                    strhtml += '<input type="text" id="input_scenarioKey" value="" data-clear-btn="true" placeholder="Upper, no space" ui-mini>';
                    strhtml += '<label for="input_scenarioDescription">Page description:</label>';
                    strhtml += '<input type="text" id="input_scenarioDescription" value="" data-clear-btn="true" placeholder="Description" ui-mini>';
                    strhtml += '<label for="input_scenarioTitre">Item titre:</label>';
                    strhtml += '<input type="text" id="input_scenarioTitre" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_scenarioResume">Item resume:</label>';
                    strhtml += '<input type="text" id="input_scenarioResume" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<label for="input_scenarioStartModule">Item start module :</label>';
                    strhtml += '<input type="text" id="input_scenarioStartModule" value="" data-clear-btn="true" placeholder="Empty for auto name" ui-mini>';
                    strhtml += '<a href="#" onclick="$.functionsChop.submitEditScenario({scenario:\'\'});" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Submit</a>';
                    strhtml += '<a href="#" onclick="$(\'#popup\').popup(\'close\');" class="ui-shadow ui-btn ui-corner-all ui-btn-inline ui-btn-b ui-mini">Cancel</a>';
                    strhtml += '</div>';
                    
                    $.functionsLib.affichePopUp('New scenario', strhtml);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.editScenario):" + er.message);
                return null;
            }
        },
        
        /**
         * @name submitEditScenario
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        submitEditScenario : function(p_params) {
            try {
                var mode = '';
                if(p_params.scenario != ""){
                    mode = 'edit';
                }else{
                    mode = 'new';
                }
                
                var input_scenarioKey = $('#input_scenarioKey').val();
                var input_scenarioDescription = $('#input_scenarioDescription').val();
                var input_scenarioTitre = $('#input_scenarioTitre').val();
                var input_scenarioResume = $('#input_scenarioResume').val();
                var input_scenarioStartModule = $('#input_scenarioStartModule').val();
                
                if((!$.functionsLib.isUndefined(input_scenarioKey))&&(input_scenarioKey != "")){
                    var tabSetting = {
                        type : "POST"
                    };

                    var tabParams = {
                        input_scenarioKey : input_scenarioKey,
                        input_scenarioDescription : input_scenarioDescription,
                        input_scenarioTitre : input_scenarioTitre,
                        input_scenarioResume : input_scenarioResume,
                        input_scenarioStartModule : input_scenarioStartModule,
                        input_mode : mode
                    };

                    var retour = $.functionsLib.callRest(domaine+"phpsql/editScenario.php", tabSetting, tabParams);

                    if(retour["strErreur"] == ""){
                        var params = {};
                        $.functionsChop.chargerListScenario(params);

                        $('#popup').popup('close');
                    }else{
                        $.functionsLib.notification(retour["strErreur"],$.functionsLib.oda_msg_color.ERROR);
                    }
                }else{
                    $.functionsLib.notification("Field scenario key mandatory",$.functionsLib.oda_msg_color.WARNING);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.submitEditModule):" + er.message);
                return null;
            }
        },
        
        /**
         * @name chargerElement
         * @desc Hello
         * @param {json} p_params
         * @returns {json}
         */
        chargerTabElts: function() {
            try {
                var tabSetting = {
                    functionRetour : $.functionsChopView.retourTabElts
                };
                
                var tabParams = {
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/getListElements.php", tabSetting, tabParams);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.chargerTabElts):" + er.message);
                return false;
            }
        },
        
        /**
         * @name addEletInCode
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        addEletInCode: function(p_elt) {
            try {
                var codeOrigine = $("#inputPopupCode").val();
                var codeDesti = codeOrigine + "[[" + p_elt + "]]";
                
                $("#inputPopupCode").val(codeDesti);
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.addEletInCode):" + er.message);
                return false;
            }
        },
        
        /**
         * @name addNewElet
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        addNewElet: function(p_params) {
            try {
                var tabSetting = {
                };
                
                var tabParams = {
                    elementKey : p_params.key
                };
                
                var retour = $.functionsLib.callRest(domaine+"phpsql/createFastElement.php", tabSetting, tabParams);
                
                var newElt = retour["data"];
                
                var codeOrigine = $("#inputPopupCode").val();
                var codeDesti = codeOrigine + "[[" + newElt + "]]";
                
                $("#inputPopupCode").val(codeDesti);
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.addEletInCode):" + er.message);
                return false;
            }
        },
        /**
         * @name uploadFile
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        uploadFile: function(p_params) {
            try {
                if(p_params.name != "NO_DATA"){
                    $.functionsLib.uploadFile(p_params);
                }else{
                    $.functionsLib.notification("Merci de soumettre un nom de l'image avant.",$.functionsLib.oda_msg_color.ERROR);
                }
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChop.uploadFile):" + er.message);
                return false;
            }
        }
    };
    
    $.functionsChopView = {
        /* Version number */
        version: $.functionsChop.version,

        /**
         * @name editElementNew
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        editElementNew: function(p_params) {
            try {
                var params_attempt = {
                    key : ""
                    , type : ""
                    , lang : ""
                    , previous : ""
                };
                
                // Merge params_attempt into object1
                var params = $.extend( params_attempt, p_params );
                
                var strhtml = "";
                
                //Form
                strhtml += '<br>New Element, pls select a type (Text for trad, Html for structure) and write a description.<br><br>';
                strhtml += '<table style="width:100%"><tr>';
                strhtml += '<td><label for="newElementDescription">Type :</label>';
                strhtml += '<select id="newElementType" data-inline="true" data-mini="true">';
                strhtml += '<option>HTML</option><option>TEXT</option><option>CST</option><option>IMG</option>';
                strhtml += '</select></td>';
                strhtml += '<td><label for="newElementDescription">Description :</label>';
                strhtml += '<input type="text" id="newElementDescription"></td>';
                strhtml += '</tr></table>';
                strhtml += '<center><input type="button" data-inline="true" data-mini="true" value="Create" onclick="$.functionsChop.createElement({key:\''+params.key+'\',lang:\''+params.lang+'\'});"></center>';
                
                //call popup
                params.content = strhtml;
                this.createPopupEditElement(params);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChopView.editElementNew : " + er.message);
                return false;
            }
        },

        /**
         * @name editElementExist
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        editElementExist: function(p_params) {
            try {
                var params_attempt = {
                    key : ""
                    , type : ""
                    , lang : g_lang
                    , previous : ""
                };
                
                // Merge params_attempt into object1
                var params = $.extend( params_attempt, p_params );
                
                //format size
                var popUpWidth = $(window).width() * 0.80;
                var popUpHeight= $(window).height() * 0.80;
                $('#popup').width(popUpWidth).addClass( "mod" );
                $('#popup').height(popUpHeight).addClass( "mod" );
                var sizeTextArea = $.functionsLib.arrondir(popUpHeight*0.4,0);
                
                //complement titre
                var strHtmlLang = "";
                if(params.type == "TEXT"){
                    var langs = $.functionsChop.getLangs();
                    strHtmlLang += ' in <select name="select-lang" id="select-lang" onchange="$.functionsChop.editElement({key : \''+params.key+'\',lang : this.value, type : \''+params.type+'\', previous : \'\'});" data-inline="true" data-mini="true">';

                    for(var indiceLangs in langs){
                        if(params.lang == langs[indiceLangs]["code"]){
                            strHtmlLang += '<option selected value="'+langs[indiceLangs]["code"]+'">'+langs[indiceLangs]["langue"]+'</option>';
                        }else{
                            strHtmlLang += '<option value="'+langs[indiceLangs]["code"]+'">'+langs[indiceLangs]["langue"]+'</option>';
                        }
                    }   
                }
                strHtmlLang += '</select>';
               
                //Form
                //TAB
                var strhtml = "";
                strhtml += '<fieldset data-role="controlgroup">';
                strhtml += '<div data-role="navbar">';
                    strhtml += '<ul>';
                        strhtml += '<li><a href="#" onclick="$.functionsChop.openTabPreview();" class="ui-btn-active">Preview</a></li>';
                        strhtml += '<li><a href="#" onclick="$.functionsChop.openTabCode(\''+params.type+'\');">Code</a></li>';
                    strhtml += '</ul>';
                strhtml += '</div>';
                strhtml += '</fieldset>';
                
                //ZONE UNDER TAB
                strhtml += '<div id="editDivPopup">';
                
                //TAB PREVIEW
                strhtml += '<div id="divPopupPreview">Error1</div>';

                //TAB CODE
                strhtml += '<div id="divPopupEdit" style="display:none;"><table style="width:100%">';
                    strhtml += '<tr>';
                        strhtml += '<td width="70%" style="vertical-align:top;">';
                            strhtml += '<div id="divPopupCode" style="width:98%">Error2</div>';
                            strhtml += '<p style="text-align:center"><a href="#" class="ui-btn ui-btn-inline ui-mini" onclick="$.functionsChop.sauvElement({key:\''+params.key+'\',lang:\''+params.lang+'\'});">Submit</a></p>';
                        strhtml += '</td>';
                        strhtml += '<td width="30%" style="vertical-align:top;font-size:small;">';
                            switch(params.type) {
                                case 'TEXT':
                                case 'HTML' :
                                    strhtml += '<p style="text-align:center"><a href="#" onclick="$.functionsChop.addNewElet({key:\''+params.key+'\'});" class="ui-btn ui-mini ui-btn-inline">Add new tag</a></p>';
                                    strhtml += '<center><b>Import tag</b></center><div id="divTabElts">Error3</div>';
                                    break;
                                case 'CST' :
                                case 'IMG' :
                                default:
                                    break;
                            }
                        strhtml += '</td>';
                    strhtml += '</tr>';
                strhtml += '</table></div>';
                
                var paramsPreview = {
                    lang : params.lang, 
                    mode : 'read',
                    key : params.key,
                    divContent : '[['+params.key+']]',
                    divId : 'divPopupPreview'
                };
                $.functionsChop.chargerElement(paramsPreview);
                
                switch(params.type) {
                    case 'TEXT':
                    case 'HTML' :
                        //load list
                        $.functionsChop.chargerTabElts();
                        
                        var paramsCode = {
                            lang : params.lang, 
                            mode : 'code',
                            key : params.key,
                            divContent : '<textarea id="inputPopupCode" style="font-size:small;family-name:arial;height:'+sizeTextArea+'px">[['+params.key+']]</textarea>',
                            divId : 'divPopupCode'
                        };
                        break;
                    case 'CST' :
                        var paramsCode = {
                            lang : params.lang, 
                            mode : 'code',
                            key : params.key,
                            divContent : 'Content : <input type="text" id="inputPopupCode" value="[['+params.key+']]">',
                            divId : 'divPopupCode'
                        };
                        break;
                    case 'IMG' :
                        var strHtmlBtUpload = '';
                        strHtmlBtUpload += '<label for="file_up">Change updated file ([['+params.key+']]) : </label>';
                        strHtmlBtUpload += '<input type="file" name="file_up" id="file_up" value="" data-clear-btn="true" onchange="javascript:$.functionsChop.uploadFile({idInput:\'file_up\',name:\'[['+params.key+']]\',folder:\'img/\'});" />';
                        
                        var paramsCode = {
                            lang : params.lang, 
                            mode : 'code',
                            key : params.key,
                            divContent : 'Name of image : <input type="text" id="inputPopupCode" value="[['+params.key+']]">'+strHtmlBtUpload,
                            divId : 'divPopupCode'
                        };
                        break;
                    default:
                        break;
                }
                $.functionsChop.chargerElement(paramsCode);
                
                //call popup
                params.complTitle = strHtmlLang;
                params.content = strhtml;
                this.createPopupEditElement(params);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChopView.editElementExist : " + er.message);
                return false;
            }
        },

        /**
         * @name createTagView
         * @desc Hello
         * @param {json} p_params
         * @returns {string}
         */
        createTagView: function(p_params) {
            try {
                var params_attempt = {
                    key : ""
                    , lang : g_lang
                    , type : ""
                    , previous : ""
                };
                
                // Merge params_attempt into object1
                var params = $.extend( params_attempt, p_params );
                
                var linkTag = "";
                
                var element = [];
                if(params.type == ""){
                    var element = $.functionsChop.getElement({key : params.key});
                }else{
                    element["key"] = params.key;
                    element["type"] = params.type;
                    element["previous"] = params.previous;
                }
                
                if(params.type != "NEW"){
                    linkTag = '<span onclick="$.functionsChop.editElement({key:\''+element["key"]+'\', lang:\''+params.lang+'\', type:\''+element['type']+'\', previous:\''+params.previous+'\'});" style="color:'+_colorType[element['type']]+';cursor: pointer;" title="Tag type : '+element['type']+'">'+element["key"]+'</span>';
                }else{
                    linkTag = '<span style="color:'+_colorType['NEW']+';cursor: pointer;" title="Tag type : NEW">'+params.key+'</span>';
                }
                
                return linkTag;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChopView.createTagView : " + er.message);
                return false;
            }
        },

        /**
         * @name createPopupEditElement
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        createPopupEditElement: function(p_params) {
            try {
                var params_attempt = {
                    key : ""
                    , lang : g_lang
                    , previous : ""
                    , content : ""
                    , complTitle : ""
                };
                
                // Merge params_attempt into object1
                var params = $.extend( params_attempt, p_params );
                
                var strhtml = "";
                
                //Titre
                var linkTag = this.createTagView(params);
                var strTitle = 'Element : '+linkTag+params.complTitle;
                
                //nav bar
                switch(params.previous) {
                    case '':
                        _navBarEdit = [];
                        break;
                    case 'escape' :
                        break;
                    default:
                        if(_navBarEdit[_navBarEdit.length] != params.previous){
                            _navBarEdit[_navBarEdit.length] = params.previous;
                        }
                        break;
                }
                
                strhtml += '<div id="navbar">';
                for(var indice in _navBarEdit){
                    var eltNavBarEdit = _navBarEdit[indice];
                    strhtml += this.createTagView({key : eltNavBarEdit}) + ' > ';
                }
                strhtml += '</div>';
                
                //sep
                strhtml += '<hr/>';
                
                //content
                strhtml += params.content;
                
                //Open popup
                $.functionsLib.affichePopUp(strTitle, strhtml);
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChopView.createPopupEditElement : " + er.message);
                return false;
            }
        },

        /**
         * @name retourTabElts
         * @desc Hello
         * @param {json} p_params
         * @returns {boolean}
         */
        retourTabElts: function(p_retour) {
            try {
                if(p_retour["strErreur"] == ""){
                    var objDataTable = $.functionsLib.objDataTableFromJsonArray(p_retour["data"]["resultat"]["data"]);

                    var strhtml = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="table_tabElts"></table></br></br>';
                    $('#divTabElts').html(strhtml).trigger('create');

                    var oTable = $('#table_tabElts').dataTable( {
                        "oLanguage": { "sSearch": "Apply filter :" },
                        "bLengthChange": false,
                        "iDisplayLength": 10,
                        "bSort": false,
                        "aaData": objDataTable.data,
                        "aoColumns": [
                            { "sTitle": "Key","sClass": "Left" },
                            { "sTitle": "Type","sClass": "Left" }
                        ],
                        "aoColumnDefs": [
                            {
                                "mRender": function ( data, type, row ) {
                                    if ( type == 'display' ) {
                                        var strHtml = "";
                                        strHtml += '<a href="#" onclick="$.functionsChop.addEletInCode(\''+row[objDataTable.entete["key"]]+'\')" style="color:'+_colorType[row[objDataTable.entete["type"]]]+';" title="Tag type : '+row[objDataTable.entete["type"]]+'">';
                                        strHtml += row[objDataTable.entete["key"]];
                                        strHtml += '</a>';
                                        return strHtml;
                                    }else{
                                        return row[objDataTable.entete["key"]];
                                    }
                                    
                                },
                                "aTargets": [ 0 ]
                            },
                            {
                                "mRender": function ( data, type, row ) {
                                    return row[objDataTable.entete["type"]];
                                },
                                "aTargets": [ 1 ]
                            }
                        ]
                    });
                } else{
                    $.functionsLib.notification("Erreur : "+p_retour["strErreur"], $.functionsLib.oda_msg_color.ERROR);
                }
                
                return true;
            } catch (er) {
                $.functionsLib.log(0, "ERROR($.functionsChopView.retourTabElts):" + er.message);
                return false;
            }
        }
    };
})();