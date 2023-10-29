function hasNumberedList(line) {
  var numberedListPattern = /^\d+\.\s/;
  return numberedListPattern.test(line);
}

function textToHtml(text) {
  var lines = text.split("\n");
  var inList = false;
  var listType = "ul";
  var html = "";

  if (!String.prototype.trim) {
    String.prototype.trim = function () {
      return this.replace(/^\s+|\s+$/g, "");
    };
  }

  for (var i = 0; i < lines.length; i++) {
    var line = lines[i].trim();

    if (/^\d+\.\s/.test(line) || line.indexOf("・") === 0) {
      if (!inList) {
        inList = true;
        listType = line.indexOf("・") === 0 ? "ul" : "ol";
        html += "<" + listType + ">";
      }
      var listItem = line
        .replace(/^\d+\.\s/, "")
        .replace("・", "")
        .trim();

        var get_number = parseInt(line.match(/^\d+/)[0]);               
        html += "<li>" + listItem + "</li>";

    } else {

      if (i > 0) {
        var check_last = hasNumberedList(lines[i - 1].trim());
        if (check_last == true) {
          inList = true;
        }
      }

      if (inList) {
        inList = false;
        html += "</" + listType + ">";
      }

      if (line !== "") {
        html += "<div>" + line + "</div>";
      }else{
        html += "<div>&nbsp;</div>";
      }

    }
  }

  if (inList) {
    inList = false;
    html += "</" + listType + ">";
  }

  return html;
}

if (document.attachEvent) {
  document.attachEvent("onreadystatechange", function () {
    if (document.readyState === "complete") {
      var inputText = document.getElementById("txt-code");
      inputText.onchange = function () {
        var htmlOutput = textToHtml(inputText.value);
        var container = document.getElementById("output_container");
        container.innerHTML = htmlOutput;
      };
    }
  });
}
