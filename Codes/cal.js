let codemirror_editor;
let codeupdate_editor;

let default_editor = 1;
let timeout_delay = 0;
let calypso_code = document.createElement("div");
let api_link_url = "http://localhost/new_calypso/_calyp.php";

let function_keymap_editor = [
  { "Alt-B": function (cm) { insert_stringin_template('<br>', codemirror_editor); } },
  { "Alt-S": function (cm) { insert_stringin_template('<div>&nbsp;</div>', codemirror_editor); } },
  { "Alt-L": function (cm) { list_selected_editor(codemirror_editor); } },
  { "Alt-O": function (cm) { ol_selected_editor(codemirror_editor); } },
  { "Alt-N": function (cm) { list_breakin_editor(codemirror_editor); } },
  { "Ctrl-B": function (cm) { modify_selected_textin_editor(codemirror_editor, '<strong>'); } },
  { "Ctrl-U": function (cm) { modify_selected_textin_editor(codemirror_editor, '<u>'); } },
  { "Ctrl-I": function (cm) { modify_selected_textin_editor(codemirror_editor, '<i>'); } },
  { "Alt-1": function (cm) { key_function_editor(codemirror_editor, 'MAJOR ITEM'); } },
  { "Alt-D": function (cm) { key_function_editor(codemirror_editor, 'DIV CONTENT') } },
  { "Alt-2": function (cm) { key_function_editor(codemirror_editor, 'MEDIUM ITEM'); } },
  { "Alt-3": function (cm) { key_function_editor(codemirror_editor, 'SMALL ITEM'); } },
  { "Alt-Q": function (cm) { key_function_editor(codemirror_editor, 'QUESTION'); } },
  { "Ctrl-1": function (cm) { key_function_editor(codemirror_editor, 'FRAME IMPORTANT'); } },
  { "Ctrl-2": function (cm) { key_function_editor(codemirror_editor, 'FRAME SUPPLEMENT 1'); } },
  { "Ctrl-3": function (cm) { key_function_editor(codemirror_editor, 'FRAME SUPPLEMENT 2'); } },
  { "Ctrl-4": function (cm) { key_function_editor(codemirror_editor, 'FRAME UPDATE'); } },
  { "Ctrl-5": function (cm) { key_function_editor(codemirror_editor, 'FRAME KNOWLEDGE'); } },
  { "Alt-Y": function (cm) { change_fw(); } },
  { "Alt-R": function (cm) { remove_fw(); } },
  { "Alt-I": function (cm) { insert_stringin_template('<img src="https://sie-calypso--c.documentforce.com/servlet/rtaImage?eid=ka03h000000quok&amp;feoid=00N3h00000ImL2A&amp;refid=0EM3h000001yTHU">', codemirror_editor); } },
  { "Alt-U": function (cm) { insert_stringin_template('<img src="https://sie-calypso--c.documentforce.com/servlet/rtaImage?eid=ka03h000000quok&amp;feoid=00N3h00000ImL2A&amp;refid=0EM3h000001yTHP">', codemirror_editor); } },
  { "Ctrl-/": function (cm) { comment_codein_editor(codemirror_editor); } },
  { "F1": function (cm) { show_help(); } },
  { "Shift-Alt-F": function (cm) { tidy_codein_editor(codemirror_editor); } },
  { "Shift-Alt-M": function (cm) { minify_codein_editor(codemirror_editor); } },
  { "Ctrl-Alt-F": function (cm) { fix_code_editor(codemirror_editor); } }
];


let code_data;
let side_temp = 1;
let addinfo_ikb = "";
let addinfo_faq = "";

const fw_list = ['(', ')', '[', ']', '「', '」', '【', '】', '『', '』', ':'];
const fix_fw = ['（', '）', '［', '］', '「', '」', '【', '】', '『', '』', '：'];
const rgb2hex = (rgb) => `#${rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/).slice(1).map(n => parseInt(n, 10).toString(16).padStart(2, '0')).join('')}`

let color_red = [];
let color_black = [];
let calyp_color = [];

let major_item_color = ['rgb(41, 128, 185)'];
let medium_item_color = ['rgb(41, 128, 185)'];
let small_item_color = ['rgb(127, 140, 141)'];
let insert_css = `<style>table{border-collapse:collapse!important}.error{background-color:red;padding:2px;border-radius:5px;border:1px solid black;color:white;margin:2px;font-size:12px}</style>`;


document.addEventListener('DOMContentLoaded', async (e) => {
  //confirm before close
  //window.onbeforeunload = function () { return "test"; }

  startup_function();

  $("span").removeClass('note-icon-caret');

  get_codes();
  get_list('list');
  get_list('circled-number');
  $('.note-statusbar').css('display', 'none');

  $(document).on("click", ".edit_code_btn", function () {
    var k = $(this).attr("id");
    code_modal(k);
  });

  $(document).on("click", '#upt_calypso_colors', function () {
    update_calypso_colors();
  });


  $(document).on("click", '#fix_code_button', function () {
    fix_code_editor(codemirror_editor);
  });


  $(document).on("click", '#check_preview', function () {
    run_checker();
  });

  $(document).on("click", '#calypso_colors', function () {
    show_colors();
  });

  window.addEventListener('resize', function (event) {
    updatePreview();
  }, true);


});

startup_function = () => {
  load_codemirror();
  load_summernote();
  //codemirror_editor.getDoc().setValue($(document).find("#kw_code").val());
},

  load_summernote = () => {

    var option_btn = function (context) {
      var ui = $.summernote.ui;
      var event = ui.buttonGroup([
        ui.button({
          contents: 'option',
          data: {
            toggle: 'dropdown'
          }
        }),
        ui.dropdown({
          items: [
            'Major Item', 'Medium Item', 'Small Item', 'Frame Important', 'Frame Supplement 1', 'Frame Supplement 2', 'Frame Update', 'Frame Knowledge', 'Question'
          ],
          callback: function (items) {
            $(items).find('.note-dropdown-item').on('click', function (e) {
              console.log(context.invoke);
              context.invoke("editor.insertText", 'test');
              e.preventDefault();
            })
          }
        })
      ]);

      return event.render();   // return button as jquery object

    }

    var export_word = function (context) {
      var ui = $.summernote.ui;
      var button = ui.button({
        contents: '<i class="fa fa-file-word"></i>',
        tooltip: 'Highlight text with red color',
        click: function () {
          html2word();
        }
      });
      return button.render();
    }

    $('#summernote').summernote({
      height: 250,
      indentUnit: 3,
      disableDragAndDrop: true,
      fontSizeUnits: ['px'],
      followingToolbar: false,
      dialogsInBody: true,
      toolbar: [
        ['style', ['bold', 'italic', 'underline']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['mybutton', ['option']]
      ],
      buttons: {
        option: export_word
      },
      // hint: {
      //   words: ['div', 'table', 'watermelon', 'lemon'],
      //   match: /\b(\w{1,})$/,
      //   search: function (keyword, callback) {
      //     callback($.grep(this.words, function (item) {
      //       return item.indexOf(keyword) === 0;
      //     }));
      //   }
      // },      

      styleTags: [
        { title: 'Major Item', tag: 'table', className: 'major', value: 'table' },
        { title: 'Medium Item', tag: 'table', className: 'medium', value: 'table' },
        { title: 'Small Item', tag: 'table', className: 'small', value: 'table' },
        { title: 'Frame Important', tag: 'table', className: 'important', value: 'table' },
        { title: 'Frame Supplement 1', tag: 'table', className: 'supplement1', value: 'table' },
        { title: 'Frame Supplement 2', tag: 'table', className: 'supplement2', value: 'table' },
        { title: 'Frame Update', tag: 'table', className: 'update', value: 'table' },
        { title: 'Frame Knowledge', tag: 'table', className: 'knowledge', value: 'table' },
        { title: 'Question', tag: 'table', className: 'question', value: 'table' },
        { title: 'Table 1', tag: 'table', className: 'table1', value: 'table' },
        { title: 'Table 2', tag: 'table', className: 'table2', value: 'table' },
        { title: 'Table 3', tag: 'table', className: 'table3', value: 'table' },
        { title: 'Table of Contents', tag: 'table', className: 'toc', value: 'table' },
        { title: 'Overview Summary', tag: 'table', className: 'recent', value: 'table' },

      ],

      popover: {
        table: [
          ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
          ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
          ['style', ['style']],
        ],
        image: [
          ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
          ['float', ['floatLeft', 'floatRight', 'floatNone']],
          ['remove', ['removeMedia']]
        ],
        link: [
          ['link', ['linkDialogShow', 'unlink']]
        ],

        air: [
          ['color', ['color']],
          ['font', ['bold', 'underline', 'clear']],
          ['para', ['ul', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture']]
        ]
      },

      callbacks: {
        onChangeCodeview: function () {
          clearTimeout(delay);
        },
        onChange: function () {
          clearTimeout(delay);
        },

        onKeydown: function (e) {

        },

        onMousedown: function (e) {
          //$('.answer').summernote({airMode: true});
        },

        onKeyup: function (e) {
          setTimeout(function () {
          }, 200);
        },

        onBlurCodeview: function () {
          codemirror_editor.getDoc().setValue($('#summernote').summernote('code'));
        },
        onBlur: function () {
          if ($('#summernote').summernote('code').length != codemirror_editor.getValue().length) {
            codemirror_editor.getDoc().setValue($('#summernote').summernote('code'));
          }
        }
      }
    })

  },

  load_codemirror = async () => {

    codemirror_editor = CodeMirror.fromTextArea(document.getElementById('cm_editor'), {
      mode: "htmlmixed",
      theme: "monokai",
      lineNumbers: true,
      dragDrop: true,
      lineWrapping: true,
      foldGutter: true,
      autoCloseTags: true,
      gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    });

    const code_mirror_initialize = (e) => {
      for (let i = 0; i < function_keymap_editor.length; i++) {
        e.addKeyMap(function_keymap_editor[i]);
      }

      e.on("change", function () {
        default_editor = 1;
        clearTimeout(timeout_delay);
        timeout_delay = setTimeout(update_summernote_preview(e), 300);
      });


      e.focus();


    }

    code_mirror_initialize(codemirror_editor);

  },

  get_code_from_kw = () => {
    return $(document).find("#kw_code").val();
  }

update_summernote_preview = (e) => {
  let w_height = window.innerHeight;
  $('.note-resizebar').hide();
  $('.note-editor').height((w_height - 50) + "px");
  $('.note-editable').height((w_height - 120) + "px");
  $('#card_data').height('130px');
  $('#txt_input').height('110px');
  $('#summernote').summernote('code', e.getValue());
  $(".CodeMirror").height((w_height - 70) + "px");
},

  insert_stringin_template = (s, e) => {
    var doc = e.getDoc();
    var cursor = doc.getCursor();
    var pos = {
      line: cursor.line,
      ch: cursor.ch
    }
    s = s.replace(/&quot;/gi, "'");
    doc.replaceRange(s, pos);
  },

  modify_selected_textin_editor = (e, s) => {
    var v = e.getSelection();
    var l = s.length;
    var t = v.substring(0, l);
    if (t == s) {
      var n = v.substr(l, v.length - ((l * 2) + 1));
      e.replaceSelection(n);
    } else {
      replace_textin_editor(s + e.getSelection() + s.replace('<', '</'), e);
    }
  },

  replace_textin_editor = (t, e) => {
    e.replaceSelection(t);
  }

get_selected_range = (e) => {
  return { from: e.getCursor(true), to: e.getCursor(false) };
},

  list_selected_editor = (e) => {

    var t = e.getSelection();

    var li_txt = '';
    var tmp_txt = "";

    code_data.find(function (p, i) {
      if (p.template == "LI") {
        li_txt = p.code.replace("xxx", t);
      }
    });

    if ($('#li_list').val() == 'none') {
      tmp_txt = li_txt.replace("stylehere", "");
    } else {
      tmp_txt = li_txt.replace("stylehere", ' style="list-style-type:&quot;' + $('#li_list').val() + '&quot;"');
      tmp_txt = tmp_txt.replace(/&quot;/gi, "'");
    }
    replace_textin_editor(tmp_txt, e);
  },

  ol_selected_editor = (e) => {
    var t = e.getSelection();
    var o = "";
    var x = "";

    code_data.find(function (p, i) {
      if (p.template == "OL") {
        o = p.code.replace("xxx", t);
      }
    });

    if ($('#ol_list').val() == "'■ '" || $('#ol_list').val() == "'※ '") {
      x = o.replace('stylehere', $('#ol_list').val() + ";margin-left:1em");
    } else {
      x = o.replace('stylehere', $('#ol_list').val());
    }

    replace_textin_editor(x, e);
    var c = e.getCursor();
    var j;
    j = (c.line - 4) - (count_lines(t) - 1);
    e.setCursor({ line: j, ch: 0 });
  },

  key_function_editor = (e, c) => {
    var t = e.getSelection();
    var m = '';
    code_data.find(function (p, i) {
      if (p.template == c) {
        m = p.code.replace("xxx", t);
        m = m.replace(/&quot;/g, "'");
      }
    });
    e.replaceSelection(m);
  },

  run_checker = () => {
    var fix_frame = document.getElementById('preview');
    var preview = fix_frame.contentDocument || fix_frame.contentWindow.document;

    $(preview).find("#page_source").find('*').each(function (i) {
      var check_marginleft = $(this).css("margin-left");

      if (check_marginleft !== "0px") {
        $(this).prepend("<span class='error'>margin-left</span>");
      }

    });

  },

  get_codes = () => {
    $.post(api_link_url, { page: 'getcodes' }).done(function (data) {
      code_data = JSON.parse(data);
      var outp = "";
      var b_top = "";
      $.each(code_data, function (key, val) {
        if (val.pos == 1) {
          outp += '<div class="btn-group-vertical" >';
          outp += '<a class="left_menu edit_code_btn" style="color:' + color_forbackground(val.bgcolor) + '; background-color:' + val.bgcolor + '" id="' + key + '" ondragstart="dragStart(event)" draggable="true">' + val.template + '</a>';
          outp += '</div>';
        } else {
          b_top += '<p class="edit_code_btn" id="' + key + '" ondragstart="dragStart(event)" draggable="true" >' + val.template + '</p>';
        }
      });
      $('#external-events').html(outp);
      $('#top_btn').html(b_top);
    });


    $.post(api_link_url, { page: 'getcolors' }).done(function (data) {
      calyp_color = JSON.parse(data);
      $.each(calyp_color, function (key, val) {
        if (val[2] == "red") {
          color_red.push(val[1]);
        } else {
          color_black.push(val[1]);
        }
      });
    });

  },

  list_breakin_editor = (e) => {
    var t = e.getSelection()
    var a = t.split(/\r\n|\r|\n/);
    var c = a.length;
    var x = "";
    for (let i = 0; i < c; i++) {
      if (a[i] !== "") {
        x += "<li>" + a[i] + "</li>\n";
      }
    }
    replace_textin_editor(x, e);
  },

  comment_codein_editor = (e) => {
    var t = e.getSelection();
    if (t == "") { return; }
    var f = t.substring(0, 4);
    if (f == "<!--") {
      var n = t.substr(4, t.length - 7);
      e.replaceSelection(n);
    } else {
      replace_textin_editor('<!--' + e.getSelection() + '-->', e);
    }
  },

  tidy_codein_editor = (e) => {
    c = e.getValue();
    c = html_beautify(c, { indent_with_tabs: true });
    e.setValue(c);
    e.refresh();
  },

  minify_codein_editor = (e) => {
    show_toast('test', 'test', 2)
  },

  fix_code_editor = (e) => {

    // var fix_frame = document.getElementById('fixcode_fr');
    var editor_code = e.getValue();

    // var preview = fix_frame.contentDocument || fix_frame.contentWindow.document;
    // preview.open();
    // preview.write("<div id='page_source'>" + editor_code + "</div>");
    // preview.close();

    var preview = document.createElement("div");
    $(preview).html("<div id='page_source'>" + editor_code + "</div>");

    var component_class = "";
    generate_addinfo(preview);
    fix_addinfo_url(preview, '.faq');
    fix_addinfo_url(preview, '.ikb');   

    fix_others(preview);

    fix_items_anchor(preview);

    var get_all = $(preview).find("#page_source");
    var all_code = $(get_all).html();

    all_code = all_code.replace(/【【/gi, "【");
    all_code = all_code.replace(/】】/gi, "】");

    $(get_all).html(all_code);

    component_class = ".toc";
    table_of_contents(preview, component_class);

    component_class = ".recent";
    recent_updates(preview, component_class);

    component_class = ".major";
    items_code(preview, component_class);

    component_class = ".medium";
    items_code(preview, component_class);

    component_class = ".small";
    items_code(preview, component_class);

    component_class = ".important";
    frame_codes(preview, component_class);

    component_class = ".supplement1";
    frame_codes(preview, component_class);

    component_class = ".supplement2";
    frame_codes(preview, component_class);

    component_class = ".update";
    frame_codes(preview, component_class);

    component_class = ".knowledge";
    frame_codes(preview, component_class);

    component_class = ".question";
    question_code(preview, component_class);

    component_class = ".faqa";
    question_code(preview, component_class);

    component_class = ".table2";
    table_codes(preview, component_class);

    component_class = ".table1";
    table_codes(preview, component_class);

    var get_code = $(preview).find('#page_source').html();
    e.setValue(get_code);

  },

  fix_base_code = () => {

    var fix_frame = document.getElementById('fixcode_fr');
    var editor_code = editor.getValue();

    var preview = fix_frame.contentDocument || fix_frame.contentWindow.document;
    preview.open();
    preview.write("<div id='page_source'>" + editor_code + "</div>");
    preview.close();

    var title_intro = `
    <div class="title_intro">
    <strong>【ナレッジ草案作成依頼用テンプレート】</strong><br>
    <ol style="margin-bottom:0em;list-style-type:disc">
    <li>使用方法やルールについては【こちら】から参照ください。</li>
    <li>素材については【こちら】から参照ください。</li>
    </ol>
    <div>&nbsp;</div>
    </div>
    `;

    $(preview).find(".major, .medium, .small").find("td").css('font-size', "16pt");
    $(preview).find("table[border='1']").css("border-collapse", "collapse");

    if ($(preview).find(".recent_title").length < 1) {
      $(preview).find(".recent").before('<span class="recent_title"><strong><font color="#ff0000">【最近のアップデート】</font></strong></span>');
    }

    if ($(preview).find(".toc_title").length < 1) {
      $(preview).find(".toc").before('<span class="toc_title"><strong><font color="#ff0000">【概要2】</font></strong></span>');
      $(preview).find(".toc").after('<div>&nbsp;</div><span><strong><font color="#ff0000">【ここから本文】</font></strong></span><br><span><strong><font color="#ff0000">【情報】</font></strong></span>');
    }

    $(preview).find(".toc").find("a").each(function (i) {
      $(this).after($(this).text());
      $(this).remove();
    });


    var all_code = "";
    $(preview).find(".base_title").find("li").each(function (i) {
      all_code += `<p style='margin-bottom:0;margin-left:36.0pt;text-indent:-18.0pt;mso-list:l0 level1 lfo6;
      tab-stops:list 36.0pt'><span style='font-size:14.0pt;
      mso-bidi-font-size:9.0pt;font-family:Wingdings;mso-fareast-font-family:Wingdings;
      mso-bidi-font-family:Wingdings'><span style='mso-list:Ignore'>l<span
      style='font:7.0pt "Times New Roman"'>&nbsp; </span></span></span><b><span
      style='font-size:14.0pt;font-family:"Meiryo UI"'>`+ $(this).text() + `</span></b><span
      style='font-size:9.0pt;font-family:"Meiryo UI"'></span></p>`;
    });

    $(preview).find(".base_title").after("<div class='title'>" + all_code + "</div>");
    $(preview).find(".base_title").remove();

    $(preview).find("ul, ol, li, div, span, p").css("margin-bottom", "0");
    $(preview).find("ul, ol, li, div, span, p").css("margin-top", "0");

    if ($(preview).find(".title_intro").length < 1) {
      $(preview).find(".title").before(title_intro);
    }

    var get_code = preview.getElementById('page_source').innerHTML;
    editor.setValue(get_code);

  },

  html2word = () => {
    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title></title></head><body>";
    var postHtml = "</body></html>";
    fix_base_code();
    var html = preHtml + editor.getValue() + postHtml;
    var converted = window.htmlDocx.asBlob(html, {
      orientation: 'portrait'
    });
    saveAs(converted, 'base_creation-' + rand_str(5, '0123456789') + '.docx');
  },

  rand_str = (len, cs) => {
    cs = cs || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var r_s = '';
    for (var i = 0; i < len; i++) {
      var r_p = Math.floor(Math.random() * cs.length);
      r_s += cs.substring(r_p, r_p + 1);
    }
    return r_s;
  },

  get_list = (tp) => {
    $.post(api_link_url, { page: "getlist", type: tp }, function (d, s) {
      var res = JSON.parse(d);
      var op = "";
      $.each(res, function (k, v) {
        op += '<option>' + v.list_value + '</option>';
      });
      if (tp == 'list') {
        $('#ol_list').html(op);
      }
      if (tp == 'circled-number') {
        $('#li_list').html(op);
      }
    });
  },

  update_calypso_colors = () => {
    var color = $("#color_update_txt").val();
    if (color == "") {
      alert("Invalid Text Value!");
      return
    }

    $.post(api_link_url, { page: 'updatecolors', color: color }).done(function (data) {

      if (data !== "") {

        $.post(api_link_url, { page: 'getcolors' }).done(function (dt) {
          calyp_color = JSON.parse(dt);
          color_black = [];
          color_red = [];
          $.each(calyp_color, function (key, val) {
            if (val[2] == "red") {
              color_red.push(val[1]);
            } else {
              color_black.push(val[1]);
            }
          });
        });
        alert("Calypso Colors Updated!");
      }
    });
  },

  count_chr = (char, string) => {
    return string.split('').reduce((acc, ch) => ch === char ? acc + 1 : acc, 0)
  },
  change_fw = () => {
    // $('.note-editable').text($('.note-editable').text() + 'asdasdadad');
    // editor.getDoc().setValue($('#summernote').summernote('code'));

    //alert("UNDER CONSTRUCTION!");
    ch_fw();

  },

  ch_fw = () => {
    var s_code = $('#summernote').summernote('code');
    for (let i = 0; i < fw_list.length; i++) {
      //var re = new RegExp(fw_list[i], 'g');
      var re = fw_list[i];
      var cnt = count_chr(re, s_code);
      if (cnt > 0) {
        for (let x = 0; x < cnt; x++) {
          s_code = s_code.replace(re, fix_fw[i]);
        }
      }
    }
    editor.getDoc().setValue(s_code);
  },

  remove_fw = () => {
    var s_code = $('#summernote').summernote('code');
    for (let i = 0; i < fw_list.length; i++) {
      var re = new RegExp('<span style="font-family: Arial;">' + fw_list[i] + '</span>', 'g');
      s_code = s_code.replace(re, fw_list[i]);
    }
    editor.getDoc().setValue(s_code);
  },

  generate_addinfo = (preview) => {

    var ikb_code = "";
    var faq_code = "";
    var get_sl = $(preview).find("a[href*='/articles/']");
    for (let i = 0; i < get_sl.length; i++) {
      var src = $(get_sl[i]).attr('href').search("http");
      var ikb_title = $(get_sl[i]).text();
      var ikb_href = $(get_sl[i]).attr('href');
      ikb_title = ikb_title.replace("IKB JP:", "");
      if (src == -1) {
        ikb_code += "<li>【<a data-lightning-target='_subtab' target='_blank' href='" + ikb_href + "'>" + ikb_title + "</a>】</li>";
      }
    }

    if (ikb_code !== "") {
      addinfo_ikb = "<ul style='list-style-type:disc' class='ikb'>" + ikb_code + "</ul>";
    }



    var get_rl = $(preview).find("a[href*='http']");

    for (let i = 0; i < get_rl.length; i++) {
      var faq_href = $(get_rl[i]).attr('href');
      var faq_title = $(get_rl[i]).text();
      var f_kosmoss = faq_href.search("k-jp-xxxx.custhelp.com");
      if (f_kosmoss == -1) {

        if (faq_title == "こちら") {
          faq_code += "<li><a target='_blank' href='" + faq_href + "'>" + faq_href + "</a></li>";
        } else {
          faq_code += "<li><a target='_blank' href='" + faq_href + "'>" + faq_title + "</a></li>";
        }

      }
    }

    if (faq_code !== "") {
      addinfo_faq = "<ul style='list-style-type:disc' class='faq'>" + faq_code + "</ul>";
    }

  },

  fix_addinfo_url = (preview, component_class) => {
    var liText = '', liList = $(preview).find(component_class).find("li"), listForRemove = [];
    $(liList).each(function () {
      var text = $(this).text();
      if (liText.indexOf('|' + text + '|') == -1)
        liText += '|' + text + '|';
      else
        listForRemove.push($(this));
    });
    $(listForRemove).each(function () { $(this).remove(); });
    $(preview).find(component_class).removeAttr('class');
  },

  table_codes = (preview, component_class) => {

    var get_tables = preview.querySelectorAll(component_class);
    for (let i = 0; i < get_tables.length; i++) {
      var elem = get_tables[i];

      $(elem).removeAttr("style");
      $(elem).find("tr, td, span").removeAttr("style");
      $(elem).find("span").removeAttr("lang");

      //removing BR element on TD start  
      $(elem).find("td").each(function (a) {
        var check_br = $(this).find("*").first();
        if ($(check_br).prop("nodeName") == "BR") {
          $(check_br).remove();
        }
      });

      //deleting elemement with no codes
      $(elem).find("*").each(function (a) {
        var check_html = $(this).html();
        if (check_html.trim() == "") {
          if ($(this).prop("nodeName") !== "BR") {
            $(this).remove();
          }
        }
      });

      //REMOVING SPAN's with txt
      $(elem).find("td").each(function (a) {
        $(this).find("span").each(function (b) {
          $(this).after($(this).html());
          $(this).remove();
        });
      });

      $(elem).find("th, td").css("border-color", "");
      if (component_class == ".table2") {
        $(elem).css("width", "80%");
        $(elem).attr("border", "1");
        $(elem).find('tr').first().find('th, td').css("background-color", '#999999');
        $(elem).find('tr').first().find('th, td').css("color", '#ffffff');
        $(elem).removeAttr("class");
      }

      if (component_class == ".table1") {
        $(elem).css("width", "80%");
        $(elem).attr("border", "1");
        var tr_tag = $(elem).find('tr');
        for (let x = 0; x < tr_tag.length; x++) {
          $(tr_tag[x]).find('th, td').first().css("background-color", '#999999');
          $(tr_tag[x]).find('th, td').first().css("color", '#ffffff');
        }
        $(elem).removeAttr("class");
      }
    }
  },

  fix_others = async (preview) => {

    //change b to strong
    $(preview).find("b").each(function (a) {
      $(this).before("<strong>" + $(this).html() + "</strong>")
      $(this).remove();
    });

    //removing elements with no code
    $(preview).find("*").each(function (a) {
      var check_html = $(this).html();
      if (check_html.trim() == "") {
        if ($(this).prop("nodeName") !== "BR" && $(this).prop("nodeName") !== "A") {
          $(this).remove();
        }
      }
    });

    //$(preview).find("tr, td, span").removeAttr("style");
    $(preview).find("*").removeAttr("lang");
    $(preview).find("*").removeAttr("language");

    var get_word_list = $(preview).find(".MsoNormal, .MsoListParagraph");
    for (let x = 0; x < get_word_list.length; x++) {
      do {
        $(get_word_list[x]).find("span").each(function (a) {
          $(this).after($(this).html());
          $(this).remove();
        });
      } while ($(get_word_list[x]).find("span").length != 0); // loop until x is 10      

      var get_html = $(get_word_list[x]).html();
      var src = get_html.search("l&nbsp;&nbsp;");
      if(src !== -1){
        get_html = get_html.replace(new RegExp('l&nbsp;&nbsp;', 'gi'), "");
        get_html = get_html.replace(new RegExp(/<!--\[if !supportLists\]-->/, 'gi'), "");
        get_html = get_html.replace(new RegExp(/<!--\[endif\]-->/, 'gi'), "");  
        $(get_word_list[x]).after("<li>" + get_html + "</li>");
        $(get_word_list[x]).remove();
      }
    }


    //converting P to BR text
    $(preview).find("p").each(function (i) {
      $(this).before("<br>" + $(this).html());
      $(this).remove();
    });


    $(preview).find("font").removeAttr("size");
    $(preview).find("*").removeAttr("dir");
    $(preview).find("*").removeAttr("rel");

    $(preview).find("span").each(function (i) {
      var attr = $(this).attr('style');
      if (typeof attr !== 'undefined' && attr !== false) {
        var src = attr.search("font-weight: bold");
        if (src !== -1) {
          $(this).html("<strong>" + $(this).html() + "</strong>");
          $(this).css("font-weight", "");
        }

        src = attr.search("border-bottom: 1px solid");
        if (src !== -1) {
          $(this).html("<u>" + $(this).html() + "</u>");
          $(this).css("border-bottom", "");
        }
      }
    });

    $(preview).find("div").each(function (i) {
      var attr = $(this).attr('style');
      if (typeof attr !== 'undefined' && attr !== false) {
        var src = attr.search("font-weight: bold");
        if (src !== -1) {
          $(this).html("<strong>" + $(this).html() + "</strong>");
          $(this).css("font-weight", "");
        }
      }
    });

    $(preview).find("font, span").each(function (i) {

      var get_color = $(this).attr("color");
      var get_s_color = $(this).css("color");

      $(this).removeAttr("face");

      for (let i = 0; i < color_red.length; i++) {
        if (color_red[i].trim() == get_color) {
          $(this).attr("color", "red");
        }

        // alert(rgb2hex(get_s_color));

        // if (color_red[i].trim() == rgb2hex(get_s_color)) {        
        //   $(this).css("color", "red");
        // }

      }

      for (let i = 0; i < color_black.length; i++) {
        if (color_black[i] == get_color) {
          $(this).removeAttr("color");
        }

        // if (color_black[i] == rgb2hex(get_s_color)) {
        //   $(this).css("color", "");
        // }      

      }

      var has_attr = this.hasAttributes();
      if (has_attr == false) {
        $(this).after($(this).html());
        $(this).remove();
      }

    });

    var get_elem = $(preview).find("*");
    $(preview).find("*").css("text-indent", "");
    $(preview).find("table").css("height", "")
    $(preview).find("table").removeAttr("width");

    $(preview).find("*:not(td)").css("padding-left", "");
    $(preview).find("*:not(td)").css("padding-top", "");
    $(preview).find("*:not(td)").css("padding-right", "");
    $(preview).find("*:not(td)").css("padding-bottom", "");

    $(preview).find(".spec_list").removeAttr("class");

    for (let i = 0; i < get_elem.length; i++) {
      var font_size = $(get_elem[i]).css("font-size");
      var get_ml = parseFloat($(get_elem[i]).css("margin-left"));
      if (font_size !== "16px") {
        $(get_elem[i]).css("font-size", "");
      } else {
        var getpt = get_elem[i].getAttribute("style");
        if (getpt !== null) {
          var src = getpt.search("12pt");
          if (src !== -1) {
            $(get_elem[i]).css("font-size", "");
          }
        }

      }

      if (get_ml < 0) { $(get_elem[i]).css("margin-left", ""); }

      var get_style = get_elem[i].getAttribute("style");
      if (get_style !== null) {
        var src = get_style.search("margin:");
        if (src !== -1) {
          var check_margin = get_style.search("margin-");
          if (check_margin == -1) {
            $(get_elem[i]).css("margin", "");
          }
        }
      }

      var get_style = get_elem[i].getAttribute("style");
      if (get_style !== null) { if (get_style == "") { $(get_elem[i]).removeAttr("style"); } }

    }


    $(preview).find("table").each(function (i) {
      var get_fontsize = $(this).find("*").css("font-size");
      if (get_fontsize == "16px") {
        var get_border_bottom_color = $(this).find("td").css("border-bottom-color");

        for (let i = 0; i < major_item_color.length; i++) {
          if (get_border_bottom_color == major_item_color[i]) {
            $(this).addClass("major");
          }
        }

        for (let i = 0; i < medium_item_color.length; i++) {
          if (get_border_bottom_color == medium_item_color[i]) {
            $(this).addClass("medium");
          }
        }

        for (let i = 0; i < small_item_color.length; i++) {
          if (get_border_bottom_color == small_item_color[i]) {
            $(this).addClass("small");
          }
        }

      }
    });

    $(preview).find("img, ul, ol, li, table").css("margin-left", "");

    var get_header = $(preview).find("h1, h2, h3, h4, h5, h6");
    for (let i = 0; i < get_header.length; i++) {
      var get_text = $(get_header[i]).html();
      $(get_header[i]).before("<div>" + get_text + "</div>");
      $(get_header[i]).remove();
    }

  }


  fix_word_list = (element) => {
    let wait_for_details = setInterval(function () {
        var check_span = $(element).find(".MsoListParagraph span");
        if (check_span.length > 0) {
            $(element).find(".MsoListParagraph").each(function (a) {
                $(this).find("span").each(function (b) {
                    $(this).after($(this).html());
                    $(this).remove();
                });
            });
        } else {
            clearInterval(wait_for_details);
        }
    }, 1);
},

    fix_items_anchor = (preview) => {

        $(preview).find(".major").each(function (a) {
            var get_anchor = $(this).prev();
            if ($(get_anchor).prop("nodeName") !== "A") {
                $(this).before("<a id='a" + (a + 1) + "' name='a" + (a + 1) + "'></a>")
            } else {
                var check_if_href = $(get_anchor).attr("href");
                if (check_if_href == undefined) {
                    $($(get_anchor).attr("id", "a" + (a + 1)));
                    $($(get_anchor).attr("name", "a" + (a + 1)));
                }
            }


            var elementsBetweenMajors = $(this).nextUntil("table.major");



            let med_fnd = 1;
            
            alert($(elementsBetweenMajors).find(".medium").length)

            elementsBetweenMajors.each(function () {

                if ($(this).hasClass("medium") == true) {

                    var get_medium_anchor = $(this).prev();
                    var major_anchor = $(get_anchor).attr("id");

                    if ($(get_medium_anchor).prop("nodeName") !== "A") {
                        $(this).before("<a id='" + major_anchor + "-b" + med_fnd + "' name='" + major_anchor + "-b" + med_fnd + "'></a>")
                    } else {
                        var check_med_if_href = $(get_medium_anchor).attr("href");
                        if (check_med_if_href == undefined) {
                            $($(get_medium_anchor).attr("id", major_anchor + "-b" + med_fnd));
                            $($(get_medium_anchor).attr("name", major_anchor + "-b" + med_fnd));
                        }
                    }

                    med_fnd += 1;

                }

            });





        });

        $(preview).find(".medium").each(function (a) {

            var get_closest_major = $(this).prevAll(".major:first");

            if (get_closest_major.length > 0) {

                var get_major_anchor = $(get_closest_major).prev();


            } else {
                alert("No nearest major table found.");
            }
        });



    },

    table_of_contents = (preview, component_class) => {

        $(preview).find(component_class).removeAttr('style');
        $(preview).find(component_class).css('font-size', '12px');
        $(preview).find(component_class).attr('border', 1);
        $(preview).find(component_class).css('width', '100%');
        $(preview).find(component_class).find('ul, li, td, tr, a').removeAttr('style');
        $(preview).find(component_class).find('td').removeAttr('colspan');
        $(preview).find(component_class).find('td').removeAttr('rowspan');
        $(preview).find(component_class).find('tr').find("td").css('vertical-align', 'top');

        let toc_td = $(preview).find(component_class);
        toc_td.html("");

        toc_td.prepend("<strong>■項目一覧</strong>");
        toc_td.append("<ul id='major-items'></ul>");
    }

table_of_contents1 = (preview, component_class) => {

    $(preview).find(component_class).removeAttr('style');
    $(preview).find(component_class).css('font-size', '12px');
    $(preview).find(component_class).attr('border', 1);
    $(preview).find(component_class).css('width', '100%');
    $(preview).find(component_class).find('ul').removeAttr('style');
    $(preview).find(component_class).find('li').removeAttr('style');
    $(preview).find(component_class).find('td').removeAttr('style');
    $(preview).find(component_class).find('td').removeAttr('colspan');
    $(preview).find(component_class).find('td').removeAttr('rowspan');
    $(preview).find(component_class).find('tr').removeAttr('style');
    $(preview).find(component_class).find('a').removeAttr('style');
    $(preview).find(component_class).find('tr').find("td").css('vertical-align', 'top');

    var td_tag = $(preview).find(component_class).find('td');

    if (td_tag.length > 1) { td_tag.css("width", "50%"); }

    for (let i = 0; i < td_tag.length; i++) {

        var get_li = $(td_tag[i]).find("ul > li");
        var get_ul_li = $(td_tag[i]).find("ul > li > ul > li");
        var get_ul_li_li = $(td_tag[i]).find("ul > li > ul > li > ul > li");
        var get_ul_li_li_li = $(td_tag[i]).find("ul > li > ul > li > ul > li > ul > li");
        var get_ul_li_li_li_li = $(td_tag[i]).find("ul > li > ul > li > ul > li > ul > li > ul > li");

        $(get_li).attr("class", "li_level_1");

        if (get_ul_li_li_li_li.length > 0) {
            $(get_ul_li_li_li_li).attr("class", "li_level_5");
            $(get_ul_li_li_li_li).parent().css("list-style-type", "disc");
        }

        if (get_ul_li_li_li.length > 0) {
            var get_lvl4 = $(td_tag[i]).find("ul > li > ul > li > ul > li > ul > li[class!='li_level_5']");
            $(get_lvl4).attr("class", "li_level_4");
            $(get_lvl4).parent().css("list-style-type", "circle");
        }

        var get_ul_li_li = $(td_tag[i]).find("ul > li > ul > li > ul > li[class!='li_level_4']");
        if (get_ul_li_li.length > 0) {
            for (let x = 0; x < get_ul_li_li.length; x++) {
                var get_class = get_ul_li_li[x].getAttribute("class");
                if (get_class !== "li_level_5") {
                    $(get_ul_li_li[x]).attr("class", "li_level_3");
                    $(get_ul_li_li[x]).parent().css("list-style-type", "disc");
                }
            }
        }

        var get_ul_li = $(td_tag[i]).find("ul > li > ul > li[class!='li_level_3']");
        if (get_ul_li.length > 0) {
            for (let x = 0; x < get_ul_li.length; x++) {
                var get_class = get_ul_li[x].getAttribute("class");
                if (get_class == "li_level_1") {
                    $(get_ul_li[x]).attr("class", "li_level_2");
                }
            }
        }

        var get_lvl = $(td_tag[i]).find(".li_level_1");
        for (let x = 0; x < get_lvl.length; x++) {
            var a_anchor = "#a" + (x + 1);


            // check for information here

            var check_anchor = $(get_lvl[x]).find("a").first().attr('href');

            if (check_anchor.substring(0, 1) !== "#") {
                break;
            }

            var a_id = $(get_lvl[x]).find("a").first().attr('href').replace("#", "");

            $(preview).find("a[name='" + a_id + "']").removeAttr("rel");
            $(preview).find("a[name='" + a_id + "']").attr('id', a_anchor.replace("#", ""));
            $(preview).find("a[name='" + a_id + "']").attr('name', a_anchor.replace("#", ""));

            // $(preview).find("#" + a_id).attr('name', a_anchor.replace("#", ""));
            // $(preview).find("#" + a_id).attr('id', a_anchor.replace("#", ""));

            $(get_lvl[x]).find("a").first().attr('href', a_anchor);

            //CHECKING FOR OTHER ANCHOR
            var check_other_achor = $(preview).find("a[href='#" + a_id + "'");
            if (check_other_achor.length > 0) {
                $(preview).find("a[href='#" + a_id + "'").attr("href", a_anchor);
            }
            //CHECKING FOR OTHER ANCHOR

            var get_lvl2 = $(get_lvl[x]).find(".li_level_2");
            if (get_lvl2.length > 0) {

                for (let y = 0; y < get_lvl2.length; y++) {
                    var b_anchor = "-b" + (y + 1);

                    //UPDATE INFORMATION ANCHOR
                    var anchor_id = a_anchor + b_anchor;
                    var b_id = $(get_lvl2[y]).find("a").first().attr('href').replace("#", "");

                    $(preview).find("a[name='" + b_id + "']").removeAttr("rel");
                    $(preview).find("a[name='" + b_id + "']").attr('id', anchor_id.replace("#", ""));
                    $(preview).find("a[name='" + b_id + "']").attr('name', anchor_id.replace("#", ""));

                    // $(preview).find("#" + b_id).attr('name', anchor_id.replace("#", ""));
                    // $(preview).find("#" + b_id).attr('id', anchor_id.replace("#", ""));


                    //UPDATE INFORMATION ANCHOR                   

                    $(get_lvl2[y]).find("a").first().attr('href', a_anchor + b_anchor);

                    //CHECKING FOR OTHER ANCHOR
                    var check_other_achor = $(preview).find("a[href='#" + b_id + "'");
                    if (check_other_achor.length > 0) {
                        $(preview).find("a[href='#" + b_id + "'").attr("href", anchor_id);
                    }
                    //CHECKING FOR OTHER ANCHOR

                    var get_lvl3 = $(get_lvl2[y]).find(".li_level_3");

                    for (let z = 0; z < get_lvl3.length; z++) {
                        var c_anchor = "-c" + (z + 1);

                        //UPDATE INFORMATION ANCHOR
                        var anchor_id = a_anchor + b_anchor + c_anchor;
                        var c_id = $(get_lvl3[z]).find("a").first().attr('href').replace("#", "");

                        $(preview).find("a[name='" + c_id + "']").removeAttr("rel");
                        $(preview).find("a[name='" + c_id + "']").attr('id', anchor_id.replace("#", ""));
                        $(preview).find("a[name='" + c_id + "']").attr('name', anchor_id.replace("#", ""));

                        // $(preview).find("#" + c_id).attr('name', anchor_id.replace("#", ""));
                        // $(preview).find("#" + c_id).attr('id', anchor_id.replace("#", ""));

                        //UPDATE INFORMATION ANCHOR

                        $(get_lvl3[z]).find("a").first().attr('href', a_anchor + b_anchor + c_anchor);

                        //CHECKING FOR OTHER ANCHOR
                        var check_other_achor = $(preview).find("a[href='#" + c_id + "'");
                        if (check_other_achor.length > 0) {
                            $(preview).find("a[href='#" + c_id + "'").attr("href", anchor_id);
                        }
                        //CHECKING FOR OTHER ANCHOR            

                        var get_lvl4 = $(get_lvl3[z]).find(".li_level_4");
                        for (let n = 0; n < get_lvl4.length; n++) {
                            var d_anchor = "-d" + (n + 1);

                            //UPDATE INFORMATION ANCHOR
                            var anchor_id = a_anchor + b_anchor + c_anchor + d_anchor;
                            var d_id = $(get_lvl4[n]).find("a").first().attr('href').replace("#", "");

                            $(preview).find("a[name='" + d_id + "']").removeAttr("rel");
                            $(preview).find("a[name='" + d_id + "']").attr('id', anchor_id.replace("#", ""));
                            $(preview).find("a[name='" + d_id + "']").attr('name', anchor_id.replace("#", ""));

                            // $(preview).find("#" + d_id).attr('name', anchor_id.replace("#", ""));
                            // $(preview).find("#" + d_id).attr('id', anchor_id.replace("#", ""));
                            //UPDATE INFORMATION ANCHOR

                            $(get_lvl4[n]).find("a").first().attr('href', a_anchor + b_anchor + c_anchor + d_anchor);

                            //CHECKING FOR OTHER ANCHOR
                            var check_other_achor = $(preview).find("a[href='#" + d_id + "'");
                            if (check_other_achor.length > 0) {
                                $(preview).find("a[href='#" + d_id + "'").attr("href", anchor_id);
                            }
                            //CHECKING FOR OTHER ANCHOR              

                            var get_lvl5 = $(get_lvl4[n]).find(".li_level_5");

                            for (let o = 0; o < get_lvl5.length; o++) {
                                var e_anchor = "-e" + (o + 1);

                                //UPDATE INFORMATION ANCHOR
                                var anchor_id = a_anchor + b_anchor + c_anchor + d_anchor + e_anchor;
                                var e_id = $(get_lvl5[o]).find("a").first().attr('href').replace("#", "");

                                $(preview).find("a[name='" + e_id + "']").removeAttr("rel");
                                $(preview).find("a[name='" + e_id + "']").attr('id', anchor_id.replace("#", ""));
                                $(preview).find("a[name='" + e_id + "']").attr('name', anchor_id.replace("#", ""));

                                // $(preview).find("#" + e_id).attr('name', anchor_id.replace("#", ""));
                                // $(preview).find("#" + e_id).attr('id', anchor_id.replace("#", ""));

                                //UPDATE INFORMATION ANCHOR

                                $(get_lvl5[o]).find("a").first().attr('href', a_anchor + b_anchor + c_anchor + d_anchor + e_anchor);

                                //CHECKING FOR OTHER ANCHOR
                                var check_other_achor = $(preview).find("a[href='#" + e_id + "'");
                                if (check_other_achor.length > 0) {
                                    $(preview).find("a[href='#" + e_id + "'").attr("href", anchor_id);
                                }
                                //CHECKING FOR OTHER ANCHOR                
                            }

                        }
                    }
                }
            }
        }

        $(preview).find(component_class).find("td").first().find('li').each(function (i) {
            var litxt = $(this).text().trim();

            if (litxt == "関連リンク") {
                $(this).find("a").attr("href", "#link");
                $(this).append("<ul><li><a href='#ikb'>IKB</a></li><li><a href='#faq'>外部FAQ</a></li></ul>");
            }

            if (litxt == "ケースコーディング") {
                $(this).find("a").attr("href", "#case");
            }

        });

        $(preview).find(component_class).find("a").each(function (i) {
            var get_text = $(this).text().trim();
            if (get_text == "関連リンク") { $(this).attr("href", "#link"); }
            if (get_text == "IKB") { $(this).attr("href", "#ikb"); }
            if (get_text == "外部FAQ") { $(this).attr("href", "#faq"); }
            if (get_text == "ケースコーディング") { $(this).attr("href", "#case"); }
        });


        $(get_li).removeAttr("class");
        $(preview).find(component_class).find("a").removeAttr('rel');

        //$(preview).find(component_class).removeAttr('class');  
        // var get_li = $(td_tag[i]).find("ul li");       
        // for (let x = 0; x < get_li.length; x++) {       
        //   $(get_li[x]).find("a").attr('href',"#a"+(x+1));     
        // }         
        // $(get_li).removeAttr("class");   
        // $(get_li).find("*").removeAttr("class");

    }

}  
