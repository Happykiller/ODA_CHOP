/* global er */
//# sourceURL=OdaApp.js
// Library of tools for the exemple
/**
 * @author FRO
 * @date 15/05/08
 */

(function() {
    'use strict';

    var
        /* version */
        VERSION = '0.1'
    ;

    ////////////////////////// PUBLIC METHODS /////////////////////////
    $.Oda.App = {
        /* Version number */
        version: VERSION,

        /**
         * @returns {$.Oda.App}
         */
        startApp: function () {
            try {
                $.Oda.Loader.load({
                    depends : [{
                        "name" : "qcm",
                        "list" : [
                            {
                                "elt" : "templates/emarg.html",
                                "type": "html",
                                target : function(data){
                                    $( "body" ).append(data);
                                }
                            }
                        ]
                    }]
                });

                $.Oda.Router.addDependencies("jsToPdf", {
                    ordered : true,
                    "list" : [
                        { "elt" : "js/html2canvas.min.js", "type" : "script"},
                        { "elt" : "js/jspdf.min.js", "type" : "script"}
                    ]
                });

                $.Oda.Router.addRoute("home", {
                    "path" : "partials/home.html",
                    "title" : "oda-main.home-title",
                    "urls" : ["","home"],
                    "middleWares":["support","auth"],
                    "dependencies" : ["hightcharts"]
                });

                $.Oda.Router.addRoute("qcm-manage", {
                    "path" : "partials/qcm-manage.html",
                    "title" : "qcm-manage.title",
                    "urls" : ["qcm-manage"],
                    "middleWares":["support","auth"],
                    "dependencies" : ["dataTables","hightcharts","jsToPdf"]
                });

                $.Oda.Router.startRooter();

                return this;
            } catch (er) {
                $.Oda.Log.error("$.Oda.App.startApp : " + er.message);
                return null;
            }
        },

        /**
         * @returns {$.Oda.App}
         */
        startQcm: function () {
            try {
                $.Oda.Router.addDependencies("jsToPdf", {
                    ordered : true,
                    "list" : [
                        { "elt" : "js/html2canvas.min.js", "type" : "script"},
                        { "elt" : "js/jspdf.min.js", "type" : "script"}
                    ]
                });

                $.Oda.Router.addRoute("home", {
                    "path" : "partials/qcm-start.html",
                    "title" : "qcm.title",
                    "urls" : ["","home"]
                });

                $.Oda.Router.addRoute("qcm", {
                    "path" : "partials/qcm.html",
                    "title" : "qcm.title",
                    "urls" : ["qcm"]
                });

                $.Oda.Router.addRoute("qcmFinish", {
                    "path" : "partials/qcmFinish.html",
                    "title" : "qcmFinish.title",
                    "urls" : ["qcmFinish"],
                    "dependencies" : ["jsToPdf"]
                });

                $.Oda.Router.addRoute("301", {
                    "path" : "partials/301.html",
                    "title" : "home.title",
                    "urls" : ["301"],
                    "system" : true
                });

                $.Oda.Router.startRooter();

                return this;
            } catch (er) {
                $.Oda.Log.error("$.Oda.App.startApp : " + er.message);
                return null;
            }
        },

        Controller : {
            /**
             * @param {Object} p_params
             * @param p_params.id
             * @returns {$.Oda.App.Controller}
             */
            displayEmarg : function (p_params) {
                try {
                    var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/rapport/emarg/"+p_params.id, { callback : function(response){
                        var strHtml = $.Oda.Display.TemplateHtml.create({
                            template : "emarg-tpl"
                            , "scope" : {
                                "title": response.data.qcmDetails.qcmTitle,
                                "trainer": response.data.qcmDetails.firstName + " " + response.data.qcmDetails.lastName,
                                "location": response.data.qcmDetails.qcmLocation,
                                "hours": response.data.qcmDetails.qcmHours,
                                "duration": response.data.qcmDetails.qcmDuration,
                                "details": response.data.qcmDetails.qcmDetails
                            }
                        });

                        var strLabel = response.data.qcmDetails.qcmName+' '+response.data.qcmDetails.qcmLang+' '+response.data.qcmDetails.qcmVersion+' '+response.data.qcmDetails.qcmDate;
                        var strFooter = '<button type="button" onclick="$.Oda.App.Controller.getPdfEmarg();" class="btn btn-info" oda-label="qcm-main.getPdfEmarg">qcm-main.getPdfEmarg</button>';

                        $.Oda.Display.Popup.open({
                            "name": "popEmarg",
                            "label": strLabel,
                            "size": "lg",
                            "footer": strFooter,
                            "details": strHtml,
                            "callback": function () {
                                for (var index in response.data.qcmDates.data){
                                    var date = response.data.qcmDates.data[index];
                                    $('#tabEmarg > thead tr:first-child').append('<th colspan="2" style="text-align: center;">'+date.date+'</th>');
                                    $('#tabEmarg > thead tr:last-child').append('<th style="text-align: center;">'+ $.Oda.I8n.get('qcm-main','morning')+'</th><th style="text-align: center;">'+ $.Oda.I8n.get('qcm-main','afternoon')+'</th>');
                                    $('#tabEmarg > tbody tr:first-child').append('<td colspan="2">&nbsp;</td>');
                                }

                                for (var index in response.data.qcmUsers.data){
                                    var user = response.data.qcmUsers.data[index];
                                    var strHtmlUser = '<tr><td>'+user.firstName+' '+user.lastName+((user.company!=='')?'<br><i>'+user.company+'</i>':'')+'</td>';
                                    for (var indexDate in response.data.qcmDates.data){
                                        var date = response.data.qcmDates.data[indexDate];
                                        var strPeriode1 = 'period1';
                                        var strPeriode2 = 'period2';
                                        for(var indexData in response.data.qcmDatas.data){
                                            var data = response.data.qcmDatas.data[indexData];
                                            if(data.sessionUserId === user.sessionUserId && data.date === date.date){
                                                if(data.period === '1'){
                                                    if(data.present === '1') {
                                                        strPeriode1 = 'yes';
                                                    }else{
                                                        strPeriode1 = 'no';
                                                    }
                                                }else if(data.period === '2'){
                                                    if(data.present === '1') {
                                                        strPeriode2 = 'yes';
                                                    }else{
                                                        strPeriode2 = 'no';
                                                    }
                                                }
                                            }
                                        }
                                        strHtmlUser += '<td><div class="circle-'+strPeriode1+'"></div></td>';
                                        strHtmlUser += '<td><div class="circle-'+strPeriode2+'"></div></td>';
                                    }
                                    strHtmlUser += '</tr>';
                                    $('#tabEmarg > tbody:last-child').append(strHtmlUser);
                                }
                            }
                        });
                    }});
                    return this;
                } catch (er) {
                    $.Oda.Log.error("$.Oda.App.controller.displayEmarg : " + er.message);
                    return null;
                }
            },
            /**
             * @returns {$.Oda.App.Controller}
             */
            getPdfEmarg : function () {
                try {
                    $.Oda.Display.Notification.info($.Oda.I8n.get('qcmFinish','waitingDl'));
                    var doc = new jsPDF();
                    doc.addHTML($('#popEmarg_content')[0], 0, 15, {
                        'background': '#fff',
                    }, function() {
                        var currentTime = new Date();
                        var annee = currentTime.getFullYear();
                        var mois = $.Oda.Tooling.pad2(currentTime.getMonth()+1);
                        var jour = $.Oda.Tooling.pad2(currentTime.getDate());
                        var strDate = annee + mois + jour;
                        doc.save('emarg_' +
                            strDate + '.pdf');
                    });
                    return this;
                } catch (er) {
                    $.Oda.Log.error("$.Oda.App.Controller.getPdfEmarg : " + er.message);
                    return null;
                }
            },
            "Home": {
                /**
                 * @returns {$.Oda.App.Controller.Home}
                 */
                start: function () {
                    try {
                        var tabInput = {
                            "userId": $.Oda.Session.id,
                            "odaLimit": 10
                        };
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/", { callback : function(response){
                            var series = [
                                {
                                    name:"Fail",
                                    data:[]
                                },
                                {
                                    name:"Success",
                                    data:[]
                                }
                            ];
                            var barsLabel = [];
                            var nbUsers = [];
                            for(var indice in response.data){
                                var elt = response.data[indice];
                                nbUsers.push(elt.nbUser);
                                barsLabel.push(elt.id+'-'+elt.name+'-'+elt.lang);
                                series[0].data.push(parseInt(elt.fail));
                                series[1].data.push(parseInt(elt.success));
                            }

                            if(series.length > 0){
                                $('#chart').highcharts({
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: 'Last QCM'
                                    },
                                    xAxis: {
                                        categories: barsLabel
                                    },
                                    yAxis: {
                                        min: 0,
                                        max: 105,
                                        endOnTick: false,
                                        title: {
                                            text: 'Response quality'
                                        },
                                        stackLabels: {
                                            enabled: true,
                                            formatter: function () {
                                                return nbUsers[this.x] + " users";
                                            },
                                            style: {
                                                fontWeight: 'bold',
                                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                                            }
                                        }
                                    },
                                    tooltip: {
                                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                                        shared: true
                                    },
                                    plotOptions: {
                                        column: {
                                            stacking: 'percent'
                                        }
                                    },
                                    series: series
                                });
                            }else{
                                $('#chart').html('no datas');
                            }

                        }},tabInput);

                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.Home.start : " + er.message);
                        return null;
                    }
                }
            },
            "ManageQcm": {
                "files": {},
                /**
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                start: function () {
                    try {
                        $.Oda.App.Controller.ManageQcm.displayQcm();
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.start : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                displayQcm: function () {
                    try {
                        var retour = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/", { callback : function(response) {
                            var objDataTable = $.Oda.Tooling.objDataTableFromJsonArray(response.data);
                            var strhtml = '<table style="width: 100%" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered hover" id="tableQcm"></table>';
                            $('#divTabQcm').html(strhtml);

                            var oTable = $('#tableQcm').DataTable({
                                "pageLength": 25,
                                "sPaginationType": "full_numbers",
                                "language" : $.Oda.I8n.getByGroupName('oda-datatables'),
                                "aaData": objDataTable.data,
                                "aaSorting": [[0, 'desc']],
                                "aoColumns": [
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","id"), "sClass": "dataTableColCenter"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","author"), "sClass": "Left"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","name"), "sClass": "Left"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","version"), "sClass": "dataTableColCenter"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","lang"), "sClass": "Left"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","date"), "sClass": "dataTableColCenter"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","desc"), "sClass": "Left"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","link"), "sClass": "Left"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","success"), "sClass": "Left"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","nbUser"), "sClass": "dataTableColCenter"},
                                    {"sTitle": $.Oda.I8n.get("qcm-manage","action"), "sClass": "Left"}
                                ],
                                "aoColumnDefs": [
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["id"]];
                                        },
                                        "aTargets": [0]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["authorCode"]];
                                        },
                                        "aTargets": [1]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["name"]];
                                        },
                                        "aTargets": [2]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["version"]];
                                        },
                                        "aTargets": [3]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return $.Oda.I8n.get("qcm-manage",row[objDataTable.entete["lang"]]);
                                        },
                                        "aTargets": [4]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["date"]];
                                        },
                                        "aTargets": [5]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["desc"]];
                                        },
                                        "aTargets": [6]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            var url = $.Oda.Context.host+"qcm.html?id="+row[objDataTable.entete["id"]];
                                            return url;
                                        },
                                        "aTargets": [7]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            var success = parseInt(row[objDataTable.entete["success"]]);
                                            var fail = parseInt(row[objDataTable.entete["fail"]]);
                                            var perc = 0;
                                            if(success !== 0 || fail !== 0){
                                                perc = success / (success + fail);
                                            }
                                            return $.Oda.Tooling.arrondir(perc * 100,2)+'%';
                                        },
                                        "aTargets": [8]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            return row[objDataTable.entete["nbUser"]];
                                        },
                                        "aTargets": [9]
                                    },
                                    {
                                        "mRender": function (data, type, row) {
                                            var datas = {
                                                "id": row[objDataTable.entete["id"]],
                                                "name": row[objDataTable.entete["name"]],
                                                "version": row[objDataTable.entete["version"]],
                                                "lang": row[objDataTable.entete["lang"]],
                                                "date": row[objDataTable.entete["date"]],
                                                "desc": row[objDataTable.entete["desc"]]
                                            };
                                            var strHtml = '<button onclick="$.Oda.App.Controller.ManageQcm.seeDetails('+$.Oda.Display.jsonToStringSingleQuote({'json':datas})+')" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span> '+ $.Oda.I8n.get('qcm-manage','details')+'</button>';
                                            strHtml += '<button onclick="$.Oda.App.Controller.displayEmarg({id:'+row[objDataTable.entete["id"]]+'});" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-th-list"></span> '+ $.Oda.I8n.get('qcm-manage','emarg')+'</button>'
                                            strHtml += '<button onclick="$.Oda.App.Controller.ManageQcm.displayStats('+$.Oda.Display.jsonToStringSingleQuote({'json':datas})+');" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-stats"></span> '+ $.Oda.I8n.get('qcm-manage','stats')+'</button>'
                                            return strHtml;
                                        },
                                        "aTargets": [10]
                                    }
                                ]
                            });
                        }});
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.displayQcm : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                formQcm: function () {
                    try {
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/search/file", {callback : function(response){
                            $.Oda.App.Controller.ManageQcm.files = {};
                            for(var indice in response.data){
                                var elt = response.data[indice];
                                if(!$.Oda.App.Controller.ManageQcm.files.hasOwnProperty(elt.name)){
                                    $.Oda.App.Controller.ManageQcm.files[elt.name] = [];
                                }
                                var obj = {
                                    "lang": elt.lang
                                }
                                $.Oda.App.Controller.ManageQcm.files[elt.name].push(obj);
                            }

                            var listName = "";
                            for(var indice in $.Oda.App.Controller.ManageQcm.files){
                                listName +='<option value="'+indice+'">'+indice+'</option>';
                            }
                            var strHtml = $.Oda.Display.TemplateHtml.create({
                                template : "formQcm"
                                , scope : {
                                    "datas": listName
                                }
                            });

                            $.Oda.Display.Popup.open({
                                "name" : "createQcm",
                                "label" : $.Oda.I8n.get('qcm-manage','createQcm'),
                                "details" : strHtml,
                                size: "lg",
                                "callback" : function(){
                                    $.Oda.Display.Table.createDataTable({
                                        "target": "tableFile",
                                        "data": response.data,
                                        option: {
                                            "aaSorting": [[3, 'desc']],
                                        },
                                        "attribute": {
                                            "name" : {
                                                "header": "Name",
                                                "value": function(data, type, full, meta, row){
                                                    return row.name;
                                                },
                                                "withFilter" : true
                                            },
                                            "version" : {
                                                "header": "Version",
                                                "value": function(data, type, full, meta, row){
                                                    return row.version;
                                                },
                                                "withFilter" : true
                                            },
                                            "lang" : {
                                                "header": "Langue",
                                                "value": function(data, type, full, meta, row){
                                                    return row.lang;
                                                },
                                                "withFilter" : true
                                            },
                                            "date" : {
                                                "header": "Date",
                                                "value": function(data, type, full, meta, row){
                                                    return row.date;
                                                },
                                                "withFilter" : true
                                            },
                                            "action" : {
                                                "header": "Action",
                                                "value": function(data, type, full, meta, row){
                                                    var datas = {
                                                        "name": row.name,
                                                        "version": row.version,
                                                        "lang": row.lang,
                                                        "date": row.date
                                                    };
                                                    return '<a onclick="$.Oda.App.Controller.ManageQcm.selectQcm('+$.Oda.Display.jsonToStringSingleQuote({'json':datas})+')" class="btn btn-primary btn-xs">'+ $.Oda.I8n.get('qcm-manage','select')+'</a>';
                                                }
                                            }
                                        }
                                    });
                                }
                            });
                        }});
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.formQcm : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} params
                 * @param {String} params.name
                 * @param {String} params.version
                 * @param {String} params.lang
                 * @param {String} params.date
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                selectQcm : function (params) {
                    try {
                        var desc = $('#desc').val();
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/", {type:'POST',callback : function(response){
                            $.Oda.App.Controller.ManageQcm.displayQcm();
                            $.Oda.Display.Popup.close({name:"createQcm"});
                        }},{
                            "name": params.name,
                            "version": params.version,
                            "lang": params.lang,
                            "date": params.date,
                            "desc": desc,
                            "userId": $.Oda.Session.id,
                            "title": $('#titleNewQcm').val(),
                            "hours": $('#hours').val(),
                            "duration": $('#duration').val(),
                            "details": $('#detailsNewQcm').val(),
                            "location": $('#location').val()
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.selectQcm : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} p_params
                 * @param p_params.id
                 * @param p_params.name
                 * @param p_params.version
                 * @param p_params.lang
                 * @param p_params.date
                 * @param p_params.desc
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                seeDetails : function (p_params) {
                    try {
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/search/", { callback : function(response){
                            var strHtml = '<span style="font-weight: bold;font-size: large;" id="spanTitleQcm">'+ $.Oda.I8n.get('qcm-manage','overviewQcm') + '</span><br>';
                            strHtml +="<ul>";
                            for(var chapterName in response.data){
                                var chapterContent = response.data[chapterName]
                                strHtml += '<li><a><span style="font-weight: bold;">'+chapterName+'</span></a><ul>';
                                for(var index in chapterContent){
                                    for(var questionName in chapterContent[index]){
                                        var questionContent = chapterContent[index][questionName];
                                        var str = (chapterName+questionName).replace(/[^a-zA-Z0-9]/g, "");
                                        strHtml += '<li id="li_'+str+'"><a id="paddoc_'+str+'">'+questionName+'</a><ul>';
                                        for(var indexResponse in questionContent){
                                            for(var responseName in questionContent[indexResponse]){
                                                if(questionContent[indexResponse][responseName]){
                                                    var colorClass = "qcmResponseRight";
                                                }else{
                                                    var colorClass = "qcmResponseWrong";
                                                }
                                                strHtml += '<li><span class="'+colorClass+'">'+responseName+' </span></li>';
                                            }
                                        }
                                        strHtml += '</ul></li>';
                                    }
                                }
                                strHtml += "</ul></li>";
                            }
                            strHtml += "</ul>";
                            $.Oda.Display.Popup.open({
                                "name": "modalDetailsQcm",
                                "label": p_params.desc + " (" + p_params.name + "-" + p_params.version + "-" + p_params.lang + "-" + p_params.date + ")",
                                "details": strHtml,
                                "size": "lg",
                                "callback": function () {
                                    $('#modalDetailsQcm_content').addClass('tree');
                                    $( '.tree li' ).each( function() {
                                        if( $( this ).children( 'ul' ).length > 0 ) {
                                            $( this ).addClass( 'parent' );
                                        }
                                    });

                                    $( '.tree li.parent > a' ).click( function( ) {
                                        $( this ).parent().toggleClass( 'active' );
                                        $( this ).parent().children( 'ul' ).slideToggle( 'fast' );
                                    });

                                    $( '#all' ).click( function() {

                                        $( '.tree li' ).each( function() {
                                            $( this ).toggleClass( 'active' );
                                            $( this ).children( 'ul' ).slideToggle( 'fast' );
                                        });
                                    });

                                    $.Oda.App.Controller.ManageQcm.startBotUsers({id: p_params.id});
                                    $.Oda.Tooling.timeout($.Oda.App.Controller.ManageQcm.startBotQuestions, 300);
                                }
                            });
                        }}, {
                            "name": p_params.name,
                            "version": p_params.version,
                            "lang": p_params.lang,
                            "date": p_params.date
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.seeDetails : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} p_params
                 * @param p_params.id
                 * @param p_params.name
                 * @param p_params.version
                 * @param p_params.lang
                 * @param p_params.date
                 * @param p_params.desc
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                displayStats : function (p_params) {
                    try {
                        var strHtml = $.Oda.Display.TemplateHtml.create({
                            template : "tpltStats"
                            , scope : {
                                id: p_params.id
                            }
                        });
                        $.Oda.Display.Popup.open({
                            "name": "modalDetailsQcm",
                            "label": p_params.desc + " (" + p_params.name + "-" + p_params.version + "-" + p_params.lang + "-" + p_params.date + ")",
                            "details": strHtml,
                            "size": "lg",
                            "callback": function () {
                                $.Oda.App.Controller.ManageQcm.displayStatsByQuestion(p_params);
                            }
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.displayStats : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} p_params
                 * @param p_params.id
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                displayStatsByQuestion : function (p_params) {
                    try {
                        if(p_params.that !== undefined){
                            $('.nav-tabs li.active').removeClass('active');
                            $(p_params.that).closest('li').addClass('active');
                        }
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/report/"+p_params.id+"/stats/", { callback : function(response){
                            if(response.data.length > 0){
                                var listData = [];
                                for(var index in response.data){
                                    var elt = response.data[index];
                                    var data = [];
                                    data.push(elt.question, parseInt(elt.number));
                                    listData.push(data);
                                }

                                $('#graph').highcharts({
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: 'Question try'
                                    },
                                    legend: {
                                        enabled: false
                                    },
                                    xAxis: {
                                        type: 'category',
                                        labels: {
                                            rotation: -80
                                        }
                                    },
                                    series: [{
                                        name: 'Try',
                                        data: listData
                                    }]
                                });
                            }else{
                                $('#graph').html('no datas');
                            }
                        }}, {
                            "id": p_params.id
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.displayStatsByQuestion : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} p_params
                 * @param p_params.id
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                displayStatsByUserGeneral : function (p_params) {
                    try {
                        $('.nav-tabs li.active').removeClass('active');
                        $(p_params.that).closest('li').addClass('active');
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/report/"+p_params.id+"/stats/users/general/", { callback : function(response){
                            if(response.data.length > 0){
                                var listData = [];
                                for(var index in response.data){
                                    var elt = response.data[index];
                                    var data = [];
                                    data.push(elt.firstName+'.'+elt.lastName.substr(0,1), parseInt(elt.number));
                                    listData.push(data);
                                }

                                $('#graph').highcharts({
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: 'Question try'
                                    },
                                    legend: {
                                        enabled: false
                                    },
                                    xAxis: {
                                        type: 'category',
                                        labels: {
                                            rotation: -80
                                        }
                                    },
                                    series: [{
                                        name: 'Try',
                                        data: listData
                                    }]
                                });
                            }else{
                                $('#graph').html('no datas');
                            }
                        }}, {
                            "id": p_params.id
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.displayStatsByUserGeneral : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} p_params
                 * @param p_params.id
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                displayStatsByUserDetails : function (p_params) {
                    try {
                        $('.nav-tabs li.active').removeClass('active');
                        $(p_params.that).closest('li').addClass('active');
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/report/"+p_params.id+"/stats/users/details/", { callback : function(response){
                            var listCate = [];
                            for(var index in response.data.listQuestions){
                                var elt = response.data.listQuestions[index];
                                listCate.push(elt.question);
                            }

                            $('#graph').highcharts({
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: 'Question try'
                                },
                                legend: {
                                    enabled: false
                                },
                                xAxis: {
                                    categories: listCate,
                                    crosshair: true,
                                    type: 'category',
                                    labels: {
                                        rotation: -80
                                    }
                                },
                                series: response.data.listResponses
                            });
                        }}, {
                            "id": p_params.id
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.displayStatsByUserDetails : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} p_params
                 * @param p_params.id
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                startBotUsers : function (p_params) {
                    try {
                        if($('#modalDetailsQcm').exists()) {
                            $.Oda.Tooling.timeout($.Oda.App.Controller.ManageQcm.startBotUsers, 5000, {id:p_params.id});
                            var call = $.Oda.Interface.callRest($.Oda.Context.rest + "api/rest/rapport/qcm/" + p_params.id + "/details/", {
                                callback: function (response) {
                                    for (var index in response.data) {
                                        var eltUser = response.data[index];
                                        if (!$('#divHorse-' + eltUser.id).exists()) {
                                            var strHtml = $.Oda.Display.TemplateHtml.create({
                                                template : "tpltHorse"
                                                , scope : {
                                                    "userId": eltUser.id,
                                                    "color": "primary",
                                                    "lastName": eltUser.lastName,
                                                    "firstName": eltUser.firstName.substr(0,1)
                                                }
                                            });
                                            $('#spanTitleQcm').after(strHtml);
                                        }
                                    }
                                }
                            });
                        }
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.startBot : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                startBotQuestions : function () {
                    try {
                        var gardian = false;
                        $('div.circlePresent').remove();
                        $('li.present').removeClass('present');
                        if($('#modalDetailsQcm').exists()) {
                            gardian = true;
                            $('[id^=divHorse-]').each(function () {
                                var id = $(this).data('id');
                                var call = $.Oda.Interface.callRest($.Oda.Context.rest + "api/rest/rapport/sessionUser/" + id + "/record/", {
                                    callback: function (response) {
                                        for(var index in response.data){
                                            var value = response.data[index];
                                            var sessionUserId = value.sessionUserId;
                                            var str = value.question.replace(/[^a-zA-Z0-9]/g, "");
                                            var divHorse = $('#divHorse-' + sessionUserId);

                                            var status = 'warning';
                                            if(value.nbErrors === "0"){
                                                status = 'success';
                                            }

                                            var strHtml = $.Oda.Display.TemplateHtml.create({
                                                template : "tpltHorse"
                                                , scope : {
                                                    "userId": value.sessionUserId,
                                                    "color": status,
                                                    "lastName": value.lastName,
                                                    "firstName": value.firstName.substr(0,1)
                                                }
                                            });

                                            var isRemove = false;

                                            if(!divHorse.hasClass('btn-'+status)){
                                                isRemove = true;
                                            }

                                            var oldDiv = divHorse.closest("li").attr("id");
                                            if(oldDiv !== ('li_'+ str)){
                                                isRemove = true;
                                            }

                                            var question = $('#paddoc_'+ str);

                                            var li_chap = question.closest('ul').closest('li');

                                            if(!li_chap.hasClass('present')){
                                                li_chap.addClass('present');
                                                li_chap.find('a span').after(' <div class="circlePresent"></div>');
                                            }

                                            if(isRemove){
                                                divHorse.remove();
                                                question.after(strHtml);
                                            }

                                            break;
                                        };
                                    }
                                },{
                                    "count": 1
                                });
                            });
                        }
                        if (gardian) {
                            $.Oda.Tooling.timeout($.Oda.App.Controller.ManageQcm.startBotQuestions, 5000);
                        }
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.startBot : " + er.message);
                        return null;
                    }
                },
                /**
                 * @param {Object} objt
                 * @returns {$.Oda.App.Controller.ManageQcm}
                 */
                detailHorse : function (objt) {
                    try {
                        var horse = $(objt);
                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/rapport/sessionUser/" + horse.data('id') + "/details/", { callback : function(response){
                            var horseDetail = response.data;

                            var strHtmlHisto = "";
                            for(var index in horseDetail.histo){
                                strHtmlHisto += '<tr><td>'+horseDetail.histo[index].id+'</td><td>'+horseDetail.histo[index].question+'</td><td>'+horseDetail.histo[index].nbErrors+'</td><td>'+horseDetail.histo[index].recordDate+'</td></tr>';
                            }

                            var strHtml = $.Oda.Display.TemplateHtml.create({
                                template : "tplDetailHorse"
                                , "scope" : {
                                    "company": horseDetail.company,
                                    "createDate": horseDetail.createDate,
                                    "contentTabHisto": strHtmlHisto,
                                    "urlGetSessionUser": $.Oda.Context.host + "qcm.html?sessionUserId=" + horseDetail.id
                                }
                            });

                            var strLabel = horseDetail.firstName + ' ' + horseDetail.lastName + ' N°' + horseDetail.id;

                            $.Oda.Display.Popup.open({
                                "name": "modalDetailHorse",
                                size: "lg",
                                "label": strLabel,
                                "details": strHtml,
                                "callback": function () {
                                }
                            });
                        }});
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.ManageQcm.detailHorse : " + er.message);
                        return null;
                    }
                }
            },
            "Qcm": {
                Session: null,
                SessionDefault: {
                    "id": "0",
                    "firstName": "",
                    "lastName": "",
                    "compagny": "",
                    "qcmId": "0",
                    "qcmName": "",
                    "qcmVersion": "",
                    "qcmLang": "",
                    "qcmDate": "",
                    "state": null
                },
                map: {},
                listCheckbox: [],
                current: "",
                steps: 0,
                currentStep: 0,
                /**
                 * @returns {$.Oda.App.Controller.Home}
                 */
                start: function () {
                    try {
                        var id = $.Oda.Router.current.args["id"];
                        if(id !== undefined){
                            $.Oda.App.Controller.Qcm.Session = $.Oda.Storage.get("QCM-SESSION-"+id);
                        }

                        if($.Oda.App.Controller.Qcm.Session === null){
                            $.Oda.Router.navigateTo({'route':'301','args':{}});
                            return this;
                        }

                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/search/", { callback : function(response){
                            var iteratorPart = 0;
                            var iteratorQuestion = 0;
                            for(var indice in response.data){
                                iteratorPart++;
                                $.Oda.App.Controller.Qcm.map["qcmId_"+iteratorPart] = false;
                                var strHtml = $.Oda.Display.TemplateHtml.create({
                                    template : "qcmTitle"
                                    , scope : {
                                        "id" : "qcmId_"+iteratorPart,
                                        "title" : indice
                                    }
                                });
                                $('#qcm').append(strHtml);
                                $.Oda.App.Controller.Qcm.steps++;

                                var qcmPart = response.data[indice];
                                for(var questionIndice in qcmPart){
                                    for(var questionTitle in qcmPart[questionIndice]){
                                        var question = qcmPart[questionIndice][questionTitle];
                                        iteratorQuestion++;
                                        var strResponses = "";
                                        var iteratorResponse = 0;
                                        for(var responseIndice in question){
                                            for(var responseTitle in question[responseIndice]){
                                                var responseBody = question[responseIndice][responseTitle];
                                                iteratorResponse++;
                                                var strOptional = "";
                                                if(responseBody){
                                                    strOptional = "required"
                                                }
                                                strResponses += $.Oda.Display.TemplateHtml.create({
                                                    template : "qcmResponse"
                                                    , scope : {
                                                        "id": "qcmId_"+iteratorPart+"_"+iteratorQuestion+"_"+iteratorResponse,
                                                        "title" : responseTitle,
                                                        "responseBody" : strOptional
                                                    }
                                                });
                                            }
                                        }
                                        $.Oda.App.Controller.Qcm.map["qcmId_"+iteratorPart+"_"+iteratorQuestion] = false;
                                        var strQuestions =  $.Oda.Display.TemplateHtml.create({
                                            template : "qcmQuestion"
                                            , scope : {
                                                "id": "qcmId_"+iteratorPart+"_"+iteratorQuestion,
                                                "titleQcm" : indice,
                                                "title" : questionTitle,
                                                "responses" : strResponses
                                            }
                                        });
                                        $('#qcm').append(strQuestions);
                                        $.Oda.App.Controller.Qcm.steps++;
                                    }
                                }
                            }
                            if($.Oda.App.Controller.Qcm.Session.state !== null){
                                $.Oda.App.Controller.Qcm.map = $.Oda.App.Controller.Qcm.Session.state;
                            }
                            $.Oda.App.Controller.Qcm.moveNext();
                        }}, {
                            "name": $.Oda.App.Controller.Qcm.Session.qcmName,
                            "version": $.Oda.App.Controller.Qcm.Session.qcmVersion,
                            "lang": $.Oda.App.Controller.Qcm.Session.qcmLang,
                            "date": $.Oda.App.Controller.Qcm.Session.qcmDate
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.Qcm.start : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.Qcm}
                 */
                moveNext: function () {
                    try {
                        if($.Oda.App.Controller.Qcm.current !== ""){
                            $.Oda.App.Controller.Qcm.map[$.Oda.App.Controller.Qcm.current] = true;
                            $.Oda.App.Controller.Qcm.Session.state = $.Oda.App.Controller.Qcm.map;
                            $.Oda.Storage.set("QCM-SESSION-"+$.Oda.App.Controller.Qcm.Session.qcmId,$.Oda.App.Controller.Qcm.Session);
                            var strState = JSON.stringify($.Oda.App.Controller.Qcm.Session.state);
                            var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/sessionUser/"+$.Oda.App.Controller.Qcm.Session.id, {type: 'PUT', callback : function(response){
                            }}, {
                                state: strState
                            });
                        }

                        $.Oda.App.Controller.Qcm.currentStep = 0;
                        for(var key in $.Oda.App.Controller.Qcm.map){
                            if($.Oda.App.Controller.Qcm.map[key]){
                                $.Oda.App.Controller.Qcm.currentStep++;
                                $("#"+key).hide();
                            }
                        }

                        $('#progressBar').width(($.Oda.App.Controller.Qcm.currentStep/$.Oda.App.Controller.Qcm.steps*100)+"%");

                        var gardian = false;
                        for(var key in $.Oda.App.Controller.Qcm.map){
                            if(!$.Oda.App.Controller.Qcm.map[key]){
                                $.Oda.Scope.Gardian.remove({id:"qcm"});
                                $("#"+key).fadeIn("slow");
                                $.Oda.App.Controller.Qcm.current = key;
                                gardian = true;
                                break;
                            }
                        }

                        //no more step, finish screan
                        if(!gardian){
                            $.Oda.Router.navigateTo({'route':'qcmFinish','args':{id:$.Oda.App.Controller.Qcm.Session.qcmId}});
                        }

                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.Qcm.moveNext : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.Qcm}
                 */
                validate: function () {
                    try {
                        $.Oda.Display.Notification.removeAll();
                        var list = $( "[id^='"+$.Oda.App.Controller.Qcm.current+"_']");
                        $.Oda.App.Controller.Qcm.listCheckbox = [];
                        for(var indice in list){
                            var elt = list[indice];
                            if(elt.id !== undefined){
                                $.Oda.App.Controller.Qcm.listCheckbox.push(elt.id);
                            }
                        }

                        var gardian = 0;
                        for(var indice in $.Oda.App.Controller.Qcm.listCheckbox){
                            var elt = $("#"+$.Oda.App.Controller.Qcm.listCheckbox[indice]);
                            if(!( (elt.prop("checked") && (elt.data('attempt') === "required") ) || (!elt.prop("checked") && (elt.data('attempt') === "")) )){
                                gardian++;
                            }
                        }

                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/sessionUser/record/", {type:'POST', callback : function(response){}},{
                            "question":$('#'+$.Oda.App.Controller.Qcm.current+' h2').html(),
                            "nbErrors":gardian,
                            "sessionUserId":$.Oda.App.Controller.Qcm.Session.id,
                        });

                        if( gardian === 0 ){
                            for(var indice in $.Oda.App.Controller.Qcm.listCheckbox){
                                var elt = $("#"+$.Oda.App.Controller.Qcm.listCheckbox[indice]);
                                elt.attr("disabled", true);
                            }
                            $.Oda.Display.Notification.success($.Oda.I8n.get("qcm","SuccessMessage"));
                            var btValidte = $("#validate-"+$.Oda.App.Controller.Qcm.current);
                            btValidte.hide();
                            var btSubmit = $("#submit-"+$.Oda.App.Controller.Qcm.current);
                            btSubmit.fadeIn();
                        }else{
                            $.Oda.Display.Notification.warning(gardian + $.Oda.I8n.get("qcm","ErrorMessage"));
                        }

                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.Qcm.validate : " + er.message);
                        return null;
                    }
                },
            },
            "QcmStart" : {
                /**
                 * @returns {$.Oda.App.Controller.QcmStart}
                 */
                start: function () {
                    try {
                        var id = $.Oda.Router.current.args["id"];
                        var sessionUserId = $.Oda.Router.current.args["sessionUserId"];

                        if(sessionUserId !== undefined){
                            var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/sessionUser/"+sessionUserId, {callback : function(response){
                                try{
                                    $.Oda.App.Controller.Qcm.Session = $.Oda.App.Controller.Qcm.SessionDefault;
                                    $.Oda.App.Controller.Qcm.Session.id = response.data.id;
                                    $.Oda.App.Controller.Qcm.Session.firstName = response.data.firstName;
                                    $.Oda.App.Controller.Qcm.Session.lastName = response.data.lastName;
                                    $.Oda.App.Controller.Qcm.Session.compagny = response.data.compagny;
                                    $.Oda.App.Controller.Qcm.Session.qcmId = response.data.qcmId;
                                    $.Oda.App.Controller.Qcm.Session.qcmName = response.data.qcmName;
                                    $.Oda.App.Controller.Qcm.Session.qcmVersion = response.data.qcmVersion;
                                    $.Oda.App.Controller.Qcm.Session.qcmLang = response.data.qcmLang;
                                    $.Oda.App.Controller.Qcm.Session.qcmDate = response.data.qcmDate;
                                    $.Oda.App.Controller.Qcm.Session.state = $.parseJSON(response.data.state);
                                    $.Oda.Storage.set("QCM-SESSION-"+$.Oda.App.Controller.Qcm.Session.qcmId, $.Oda.App.Controller.Qcm.Session);
                                }catch (er) {
                                    $.Oda.App.Controller.Qcm.Session = null;
                                    $.Oda.Display.Notification.warning($.Oda.I8n.get('qcmStart','stateCompromise'));
                                    $.Oda.Router.navigateTo({'route':'301','args':{}});

                                }
                                $.Oda.Router.navigateTo({
                                    'route': 'qcm',
                                    'args': {"id": $.Oda.App.Controller.Qcm.Session.qcmId}
                                });
                            }});
                            return this;
                        }else if(id === null){
                            $.Oda.Router.navigateTo({'route':'301','args':{}});
                            return this;
                        }

                        $.Oda.App.Controller.Qcm.Session = $.Oda.Storage.get("QCM-SESSION-"+id, $.Oda.App.Controller.Qcm.SessionDefault);

                        if( (id === $.Oda.App.Controller.Qcm.Session.qcmId) ) {
                            $.Oda.Router.navigateTo({
                                'route': 'qcm',
                                'args': {"id": $.Oda.App.Controller.Qcm.Session.qcmId}
                            });
                            return this;
                        }

                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/qcm/"+id, {callback : function(response){
                            $.Oda.App.Controller.Qcm.Session.qcmId = id;
                            $.Oda.App.Controller.Qcm.Session.qcmName = response.data.name;
                            $.Oda.App.Controller.Qcm.Session.qcmVersion = response.data.version;
                            $.Oda.App.Controller.Qcm.Session.qcmLang = response.data.lang;
                            $.Oda.App.Controller.Qcm.Session.qcmDate = response.data.date;
                            $.Oda.App.Controller.Qcm.Session.qcmDesc = response.data.desc;
                            $.Oda.Storage.set("QCM-SESSION-"+$.Oda.App.Controller.Qcm.Session.qcmId, $.Oda.App.Controller.Qcm.Session);

                            $.Oda.Scope.Gardian.add({
                                id : "qcmStart",
                                listElt : ["firstName", "lastName"],
                                function : function(e){
                                    if( ($("#firstName").data("isOk")) && ($("#lastName").data("isOk")) ){
                                        $("#submit").removeClass("disabled");
                                        $("#submit").removeAttr("disabled");
                                    }else{
                                        $("#submit").addClass("disabled");
                                        $("#submit").attr("disabled", true);
                                    }
                                }
                            });
                        }});
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.QcmStart.start : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.QcmStart}
                 */
                submit: function () {
                    try {
                        $.Oda.App.Controller.Qcm.Session.firstName = $('#firstName').val();
                        $.Oda.App.Controller.Qcm.Session.lastName = $('#lastName').val();
                        $.Oda.App.Controller.Qcm.Session.company = $('#company').val();

                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/sessionUser/", {type:'POST', callback : function(response){
                            $.Oda.App.Controller.Qcm.Session.id = response.data;
                            $.Oda.Storage.set("QCM-SESSION-"+$.Oda.App.Controller.Qcm.Session.qcmId,$.Oda.App.Controller.Qcm.Session);
                            $.Oda.Router.navigateTo({'route':'qcm','args':{id:$.Oda.App.Controller.Qcm.Session.qcmId}});
                        }},{
                            "firstName": $.Oda.App.Controller.Qcm.Session.firstName,
                            "lastName": $.Oda.App.Controller.Qcm.Session.lastName,
                            "company": $.Oda.App.Controller.Qcm.Session.company,
                            "qcmId": $.Oda.App.Controller.Qcm.Session.qcmId,
                            "qcmName": $.Oda.App.Controller.Qcm.Session.qcmName,
                            "qcmLang": $.Oda.App.Controller.Qcm.Session.qcmLang
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.QcmStart.submit : " + er.message);
                        return null;
                    }
                }
            },
            "QcmFinish": {
                /**
                 * @returns {$.Oda.App.Controller.QcmFinish}
                 */
                start: function () {
                    try {
                        var id = $.Oda.Router.current.args["id"];
                        if(id !== undefined){
                            $.Oda.App.Controller.Qcm.Session = $.Oda.Storage.get("QCM-SESSION-"+id);
                        }

                        if($.Oda.App.Controller.Qcm.Session === null){
                            $.Oda.Router.navigateTo({'route':'301','args':{}});
                            return this;
                        }

                        $('#trainee').html($.Oda.App.Controller.Qcm.Session.firstName + ' ' + $.Oda.App.Controller.Qcm.Session.lastName + ' - ' + $.Oda.Date.getStrDateFR());
                        $('#qcm').html(
                            $.Oda.App.Controller.Qcm.Session.qcmName + " " +
                            $.Oda.App.Controller.Qcm.Session.qcmVersion + " " +
                            $.Oda.App.Controller.Qcm.Session.qcmLang + " " +
                            $.Oda.App.Controller.Qcm.Session.qcmDate
                        );

                        var call = $.Oda.Interface.callRest($.Oda.Context.rest+"api/rest/rapport/sessionUser/"+$.Oda.App.Controller.Qcm.Session.id+"/stats/", {callback : function(response){
                            var perc = parseInt($.Oda.Tooling.arrondir((response.data.nbTest - response.data.nbFail) / response.data.nbTest * 100,0));
                            var rest = perc % 5;
                            $('.overlay').html(perc+'%');
                            $('#progress').addClass('progress-'+(perc-rest));
                            if(perc >= 80){
                                $('.overlay').css('background-color','#f1c40f');
                                $('#medal').html($.Oda.I8n.get('qcmFinish','gold'));
                                $('#medal').addClass('gold');
                            }else if(perc >= 50 && perc < 80){
                                $('.overlay').css('background-color','#bdc3c7');
                                $('#medal').html($.Oda.I8n.get('qcmFinish','silver'));
                                $('#medal').addClass('silver');
                            }else if(perc < 50){
                                $('.overlay').css('background-color','#e67e22');
                                $('#medal').html($.Oda.I8n.get('qcmFinish','bronze'));
                                $('#medal').addClass('bronze');
                            }
                        }});

                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.QcmFinish.start : " + er.message);
                        return null;
                    }
                },
                /**
                 * @returns {$.Oda.App.Controller.QcmFinish}
                 */
                getPdfCertificate: function () {
                    try {
                        $.Oda.Display.Notification.info($.Oda.I8n.get('qcmFinish','waitingDl'));
                        var doc = new jsPDF();
                        doc.addHTML($('#certificate')[0], 0, 15, {
                            'background': '#fff',
                        }, function() {
                            var currentTime = new Date();
                            var annee = currentTime.getFullYear();
                            var mois = $.Oda.Tooling.pad2(currentTime.getMonth()+1);
                            var jour = $.Oda.Tooling.pad2(currentTime.getDate());
                            var strDate = annee + mois + jour;
                            doc.save('medal_'+$.Oda.App.Controller.Qcm.Session.qcmName + "-" +
                                $.Oda.App.Controller.Qcm.Session.qcmVersion + "-" +
                                $.Oda.App.Controller.Qcm.Session.qcmLang + "-" +
                                $.Oda.App.Controller.Qcm.Session.qcmDate+ "_" +
                                strDate + '.pdf');
                        });
                        return this;
                    } catch (er) {
                        $.Oda.Log.error("$.Oda.App.Controller.QcmFinish.getPdfCertificate : " + er.message);
                        return null;
                    }
                }
            }
        }
    };

})();