
function secondsToMinutesAndSeconds(seconds) {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  const paddedSeconds = remainingSeconds.toString().padStart(2, '0');
  return `${minutes}:${paddedSeconds}`;
}

// Test the function
const seconds = 150;
console.log(secondsToMinutesAndSeconds(seconds)); // Output: "2:30"



(function($) {
    "use strict"

    let i = 0

    function Modal(props) {
      this.props = {
        title: "",
        body: "",
        footer: "",
        modalClass: "fade",
        modalDialogClass: "",
        options: {
          //backdrop: 'static'              
        },
        onCreate: null,
        onShown: null,
        onDispose: null,
        onSubmit: null
      }
      for (let prop in props) {
        this.props[prop] = props[prop]
      }
      this.id = "bootstrap-show-modal-" + i
      i++
      this.show()
      if (this.props.onCreate) {
        this.props.onCreate(this)
      }
    }

    Modal.prototype.createcontainer_elementement = function() {
      const self = this
      this.element = document.createElement("div")
      this.element.id = this.id
      this.element.setAttribute("class", "modal " + this.props.modalClass)
      this.element.setAttribute("tabindex", "-1")
      this.element.setAttribute("role", "dialog")
      this.element.setAttribute("aria-labelledby", this.id)
      this.element.innerHTML = '<div class="modal-dialog ' + this.props.modalDialogClass + '" role="document">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<h5 class="modal-title"></h5>' +
        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">' +
        '</button>' +
        '</div>' +
        '<div class="modal-body"></div>' +
        '<div class="modal-footer"></div>' +
        '</div>' +
        '</div>'
      document.body.appendChild(this.element)
      this.titleElement = this.element.querySelector(".modal-title")
      this.bodyElement = this.element.querySelector(".modal-body")
      this.footerElement = this.element.querySelector(".modal-footer")
      $(this.element).on('hidden.bs.modal', function() {
        self.dispose()
      })
      $(this.element).on('shown.bs.modal', function() {
        if (self.props.onShown) {
          self.props.onShown(self)
        }
      })
    }

    Modal.prototype.show = function() {
      if (!this.element) {
        this.createcontainer_elementement()
        if (this.props.options) {
          const modalInstance = new bootstrap.Modal(this.element, this.props.options)
          if (modalInstance) {
            modalInstance.show()
          }
        } else {
          const modalInstance = new bootstrap.Modal(this.element)
          if (modalInstance) {
            modalInstance.show()
          }
        }
      } else {
        const modalInstance = bootstrap.Modal.getInstance(this.element)
        if (modalInstance) {
          modalInstance.show()
        }
      }
      if (this.props.title) {
        $(this.titleElement).show()
        this.titleElement.innerHTML = this.props.title
      } else {
        $(this.titleElement).hide()
      }
      if (this.props.body) {
        $(this.bodyElement).show()
        this.bodyElement.innerHTML = this.props.body
      } else {
        $(this.bodyElement).hide()
      }
      if (this.props.footer) {
        $(this.footerElement).show()
        this.footerElement.innerHTML = this.props.footer
      } else {
        $(this.footerElement).hide()
      }
    }

    Modal.prototype.hide = function() {
      const modalInstance = bootstrap.Modal.getInstance(this.element)
      if (modalInstance) {
        modalInstance.hide()
      }
    }

    Modal.prototype.dispose = function() {
      const modalInstance = bootstrap.Modal.getInstance(this.element)
      if (modalInstance) {
        modalInstance.dispose()
      }
      document.body.removeChild(this.element)
      if (this.props.onDispose) {
        this.props.onDispose(this)
      }
    }

    $.extend({
      show_modal: function(props) {
        if (props.buttons) {
          let footer = ""
          for (let key in props.buttons) {
            const buttonText = props.buttons[key]
            footer += '<button type="button" class="btn btn-primary" data-value="' + key + '" data-bs-dismiss="modal">' + buttonText + '</button>'
          }
          props.footer = footer
        }
        return new Modal(props)
      },
      show_alert: function(props) {
        props.buttons = {
          OK: 'OK'
        }
        return this.show_modal(props)
      },
      show_confirm: function(props) {
        props.footer = '<button class="btn btn-primary btn-true">' + props.textTrue + '</button><button class="btn btn-secondary btn-false btn-cancel">' + props.textFalse + '</button>'
        props.onCreate = function(modal) {
          $(modal.element).on("click", ".btn", function(event) {
            event.preventDefault()
            const modalInstance = bootstrap.Modal.getInstance(modal.element)
            if (modalInstance) {
              modalInstance.hide()
            }
            modal.props.onSubmit(event.target.getAttribute("class").indexOf("btn-true") !== -1, modal)
          })
        }
        return this.show_modal(props)
      }
    })

  }(jQuery))