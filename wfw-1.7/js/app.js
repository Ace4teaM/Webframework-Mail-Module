/*
    ---------------------------------------------------------------------------------------------------------------------------------------
    (C)2013 Thomas AUGUEY <contact@aceteam.org>
    ---------------------------------------------------------------------------------------------------------------------------------------
    This file is part of WebFrameWork.

    WebFrameWork is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WebFrameWork is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WebFrameWork.  If not, see <http://www.gnu.org/licenses/>.
    ---------------------------------------------------------------------------------------------------------------------------------------
*/

/**
 * Implémentation de l'application ExtJS
 * 
 **/

//application class
Ext.application({
    name: 'Webframework-Mailing-Module',
    appFolder: 'application',
    enableQuickTips:true,
    controllers: [
    ],
    autoCreateViewport: false,
    launch: function() {
        YUI(wfw_yui_config(wfw_yui_base_path)).use('node', 'event', function (Y)
        {
            //using Ext-js ?
            for(var index in MyApp.Loading.callback_list){
                var func = MyApp.Loading.callback_list[index];
                func(Y);
            }
        });
    }
});

//globals variables
Ext.define('MyApp.global.Vars', {
    statics: {
        //viewport
        statusPanel : null,
        contentPanel : null,
        menuPanel : null,
        footerPanel : null,
        viewport : null
    }
});

//loading functions
//ajoutez à ce global les fonctions d'initialisations
Ext.define('MyApp.Loading', {
    statics: {
        callback_list : [
            /*--------------------------------------
             *  Initialise le viewport
             *  convertie le HTML existant en layout dynamique
             --------------------------------------*/
            function (Y) {
                var wfw = Y.namespace("wfw");
                
                //cache le contenu
                var original = Y.Node.all("body > *");

                var g = MyApp.global.Vars;

                //north
                g.statusPanel = Ext.create('Ext.Panel', {
                    header:false,
                    layout: 'hbox',
                    region: 'north',     // position for region
                    split: true,         // enable resizing
                    margins: '0 5 5 5',
                    /*html: Y.Node.one("#menu").get("innerHTML")*/
                    items: [{
                        header:false,
                        border: false,
                        width:200,
                        html: Y.Node.one("#header").get("innerHTML")
                    },{
                        header:false,
                        width:"100%",
                        border: false,
                        html: Y.Node.one("#status").get("innerHTML")
                    }],
                    renderTo: Ext.getBody()
                });

                //center
                g.contentPanel = Ext.create('Ext.Panel', {
                    header :false,
                    //title: 'Content',
                    region: 'center',     // position for region
                    height: 100,
                    split: true,         // enable resizing
                    margins: '0 5 5 5',
                    layout: 'vbox',
                    autoScroll:true,
                    //html: Y.Node.one("#content").get("innerHTML")
                    items: [{
                        header:false,
                        border: false,
                        width:"100%",
                        html: Y.Node.one("#result").get("innerHTML")
                    },{
                        header:false,
                        border: false,
                        width:"100%",
                        html: Y.Node.one("#content").get("innerHTML")
                    }],
                    renderTo: Ext.getBody()
                });

                //west
                g.menuPanel = Ext.create('Ext.Panel', {
                    title: 'Menu',
                    layout: {
                        // layout-specific configs go here
                        type: 'accordion',
                        titleCollapse: false,
                        animate: true,
                        activeOnTop: true
                    },
                    region: 'west',     // position for region
                    width: 200,
                    split: true,         // enable resizing
                    margins: '0 5 5 5',
                    /*html: Y.Node.one("#menu").get("innerHTML")*/
                    items: [{
                        title: 'Administrateur',
                        html: Y.Node.one("#menu1").get("innerHTML")
                    },{
                        title: 'Visiteur',
                        html: Y.Node.one("#menu2").get("innerHTML")
                    },{
                        title: 'Utilisateur',
                        html: Y.Node.one("#menu3").get("innerHTML")
                    }],
                    renderTo: Ext.getBody()
                });

                //south
                g.footerPanel = Ext.create('Ext.Panel', {
                    header :false,
                    //title: 'Pied de page',
                    region: 'south',     // position for region
                    split: true,         // enable resizing
                    margins: '0 5 5 5',
                    html: Y.Node.one("#footer").get("innerHTML")
                });

                //viewport
                g.viewport = Ext.create('Ext.Viewport', {
                    layout: 'border',
                    items: [g.contentPanel,g.menuPanel,g.statusPanel,g.footerPanel]
                });
                
                original.remove();
            }
        ]
    }
});

/*--------------------------------------
 *  Initialise
 --------------------------------------
YUI(wfw_yui_config(wfw_yui_base_path)).use('node', 'event', function (Y)
{
    var wfw = Y.namespace("wfw");

    //connection status change
    var onLoad = function(e)
    {
        for(var index in MyApp.Loading.callback_list){
            var func = MyApp.Loading.callback_list[index];
            func(Y);
        }
    };
    
    //initialise les evenements
    Y.one('window').on('load', onLoad);
});
*/

/*------------------------------------------------------------------------------------------------------------------*/
/**
 * @brief Convertie les formulaires HTML présents en formulaire dynamique ExtJS
 * @param object Y       YahooUI Instance
 * @param string formId  Identificateur du formulaire
 * */
/*------------------------------------------------------------------------------------------------------------------

function convertHTMLForm(Y,formId)
{
    var g = MyApp.global.Vars;
    var formEl = Y.Node.one("#"+formId);

    //champs
    var items=[];
    formEl.all("fieldset").each(function(fieldsetEl){
        var fieldset={
            xtype:'fieldset',
            checkboxToggle:true,
            title: fieldsetEl.one("legend").get("text"),
            defaultType: 'textfield',
            collapsible: true,
            layout: 'anchor',
            defaults: {
                anchor: '100%'
            },
            items:[]
        };
        fieldsetEl.all("div.wfw_edit_field").each(function(fieldEl){
            var label = fieldEl.one("label");
            var node = fieldEl.one("input");
            if(!node||!label)
                return;
            var id = node.get("id");
            var name = node.get("name");
            var value = node.get("value");
            var type = node.get("className");

            //alert(id+","+type+","+value);
            var item;
            switch(type){
                case "cInputString":
                    item={
                        xtype: 'textfield',
                        id:name,
                        name:name,
                        fieldLabel: label.get("text"),
                        height:20
                    };
                    break;
                case "cInputText":
                    item={
                        xtype: 'htmleditor',
                        id:name,
                        name:name,
                        fieldLabel: label.get("text"),
                        height:250,
                        enableColors: false,
                        enableAlignments: false
                    };
                    break;
                default:
                    item={
                        xtype: 'textfield',
                        id:name,
                        name:name,
                        fieldLabel: label.get("text"),
                        height:20
                    };
                    break;
            }
            fieldset.items.push(item);
        });
        items.push(fieldset);
    });

    //boutons additionnels
    var buttons=[];
    formEl.all("#buttons input[type=button]").each(function(btnElement){
        buttons.push({
            text: btnElement.get("value")
            });
    });
                
    //submit button
    var submitBtn = formEl.one("#buttons input[type=submit]");
    buttons.push(
    {
        text: ((submitBtn) ? submitBtn.get("value") : "Envoyer"),
        handler: function() {
            var form = this.up('form').getForm();
            if (form.isValid()) {
                form.submit({
                    success: function(form, action) {
                        Ext.Msg.alert('Success', action.result.message);
                    },
                    failure: function(form, action) {
                        Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
                    }
                });
            } else {
                Ext.Msg.alert( "Error!", "Your form is invalid!" );
            }
        }
    });
                
    //formulaire
    var form = Ext.create('Ext.form.Panel', {
        id:formEl.get("id"),
        url:formEl.get("action"),
        frame:true,
        title: false,
        width: "100%",
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 180
        },
        defaultType: 'textfield',
        defaults: {
            anchor: '100%'
        },

        items: items,
        buttons: buttons
    });
                
    formEl.remove();
                
    g.contentPanel.add(form);
}*/