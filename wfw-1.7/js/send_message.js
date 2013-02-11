/*
 *
 **/
YUI(wfw_yui_config(wfw_yui_base_path)).use('node', 'event', 'editor', 'Ext.form.*', function (Y)
{
    var wfw = Y.namespace("wfw");
    
    //connection status change
    var onLoad = function(e)
    {
        /*var editor = new Y.EditorBase({
            content: '<strong>This is <em>a test</em></strong> <strong>This is <em>a test</em></strong> '
        });

        //Add the BiDi plugin
        editor.plug(Y.Plugin.EditorBidi);

        //Focusing the Editor when the frame is ready..
        editor.on('frame:ready', function() {
            this.focus();
        });

        //Rendering the Editor.
        editor.render('#txt_msg');
        */
       /* var formPanel = Ext.create('Ext.form.Panel', {
            frame: true,
            title: 'Form Fields',
            width: 640,
            bodyPadding: 5,

            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 150,
                anchor: '100%'
            },

            items: [{
                xtype: 'emailfield',
                name: 'from',
                fieldLabel: 'Text field',
                fieldLabel: "Adresse de l'expéditeur"
            }, {
                xtype: 'textfield',
                name: 'from_name',
                fieldLabel: "Nom de l'expéditeur"
            },{
                xtype: 'emailfield',
                name: 'to',
                fieldLabel: "Adresse du destinataire"
            }, {
                xtype: 'filefield',
                name: 'subject',
                fieldLabel: 'Sujet'
            }, {
                xtype: 'textareafield',
                name: 'msg',
                fieldLabel: 'Message',
                value: 'Entrez votre message ici'
            }, {
                xtype: 'displayfield',
                name: 'template',
                fieldLabel: 'Nom du fichier template',
                value: 'default.html'
            }, {
                xtype: 'textfield',
                name: 'notify',
                fieldLabel: 'Adresse de notification',
                value: 5,
                minValue: 0,
                maxValue: 50
            }, {
                xtype: 'checkboxfield',
                name: 'checkbox1',
                fieldLabel: 'Checkbox',
                boxLabel: 'box label'
            }]
        });

        formPanel.render('inputs');*/
    };
    
    //initialise les evenements
    Y.one('window').on('load', onLoad);
});
