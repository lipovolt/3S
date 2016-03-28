seajs.config({
  alias: {
    "jquery": "lib/jquery-1.8.3.js",
    "validate": "lib/jquery.validate.min.js",	
    "dialog": "lib/jquery-ui-dialog.js",
    "jquery.ui": "lib/jquery-ui.min.js",
    "timepicker": "lib/jquery-ui-timepicker-addon.min.js",
    "datatable":"lib/jquery.dataTables.js",
    "validform_plugin":"lib/Validform_v5.3.2_min.js",
    "validform":"lib/validform",
    "chosen":"lib/chosen/chosen.jquery.min.js"
  },
  preload: [
	"lib/common.js"
  ],
  debug: false,
  map: [
    ['', '']
  ],
  charset: 'utf-8'
});