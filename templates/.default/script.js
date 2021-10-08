(function ($) {

    class Halscomments {

        constructor(params, messages) {
            this.params = params;
            this.messages = messages;
        }

        // добавление обработчиков на элементы управления
        init() {
            $("#rundesignercommentssend").click(this.clickSend)
        }

        clickSend(e) {
            e.preventDefault();
            if (!window.rundesignercomments._validateForm()) {
                alert(window.rundesignercomments.messages.wrongform);
                return;
            }else{
                console.log("hc form validated");
            }
            
            let commentdata = {
                imya: $("#rundesignercomment-input-imya").val(),
                email: $("#rundesignercomment-input-email").val(),
                text: $("#rundesignercomment-input-text").val()
            }
            window.rundesignercomments.postComment(commentdata)
        }

        //получение списка комментариев
        getComments() {
            let $this = this;
            BX.ajax.runComponentAction('rundesigner:rundesigner.comments',
                    'getComments', {// Вызывается без постфикса Action
                        mode: 'class',
                        data: {postdata: this.params}, // ключи объекта data соответствуют параметрам метода
                    })
                    .then(function (response) {
                        $this._renderComments(response.data)
                    }, function () {
                        alert(this.messages.error)
                    });
        }

        //отправляем комментарий на сервер  
        postComment(commentdata) {
            let $this = this;
            BX.ajax.runComponentAction('rundesigner:rundesigner.comments',
                    'postComment', {// Вызывается без постфикса Action
                        mode: 'class',
                        data: {postdata: this.params, commentdata: commentdata}, // ключи объекта data соответствуют параметрам метода
                    })
                    .then(function (response) {
                        $this._renderComments(response.data)
                        $this._afterComment()
                    }, function () {
                        alert(this.messages.error)
                    });
        }

//выведем комментарии полученные с сервера
        _renderComments(data) {

            if (data === null || data.length === 0) {
                $("#rundesignernocomments").removeClass("d-none").addClass("d-flex");
            } else {
                $("#rundesignernocomments").removeClass("d-flex").addClass("d-none");
                    $(".hcrows").remove();
                    let rowtemplate = $("#hcrowtemplate");
                    data.forEach(rowdata => {
                        let rowel = rowtemplate.clone();
                        rowel.attr("id", "hc" + rowdata.id);
                        rowel.removeClass("d-none");
                        rowel.addClass("hcrows");
                        rowel.find(".textauthor").text(rowdata.imya);
                        rowel.find(".hcdate").text(rowdata.comentdate);
                        rowel.find(".textcomment").html(rowdata.comment);
                        rowel.find(".hcemail").attr("href","mailto:"+rowdata.email);
                        $("#rundesignernocomments").after(rowel);
                    });
            }
        }

//очистим форму покажем сообщение, что комментарий добавлен
        _afterComment() {
            $("#rundesignercomment-input-imya").val('');
            $("#rundesignercomment-input-email").val('');
            $("#rundesignercomment-input-text").val('');
        }

        _validateForm() {
            let ret = true;
            //если неавторизован
            if (!$("#hcimyaemail").hasClass("d-none")) {
                console.log("hc not authorized");
                if ($("#rundesignercomment-input-imya").val().length < 3) {
                    console.log("hc error wrong imya");
                    ret = false;
                }
                if (!this._validateEmail($("#rundesignercomment-input-email").val())) {
                    console.log("hc error wrong email");
                    ret = false;
                }
            }else{
                console.log("hc authorized");
            }

            if ($("#rundesignercomment-input-text").val().length < 3) {
                console.log("hc error wrong text");
                ret = false;
            }

            return ret;
        }

        _validateEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

    } //end class rundesignercomments


    $(function () {
        //если не настроены параметры ничего не делаем
        if ($("#rundesignercommentsparamserror").length > 0) {
            return;
        }
        let params = {
            iblockid: $("#rundesignercommentsdata").data("param-iblockid"),
            propertyelementid: $("#rundesignercommentsdata").data("param-propertyelementid"),
            propertyemail: $("#rundesignercommentsdata").data("param-propertyemail"),
            propertyname: $("#rundesignercommentsdata").data("param-propertyname"),
            elementid: $("#rundesignercommentsdata").data("param-elementid")
        };
        let messages = {
            error: $("#rundesignercommentsdata").data("message-error"),
            wrongform: $("#rundesignercommentsdata").data("message-wrongform")
        };
        window.rundesignercomments = new Halscomments(params, messages);
        window.rundesignercomments.init();
        //получаем комментарии
        window.rundesignercomments.getComments($("#rundesignercommentsdata").data("elementid"));
    });

})(jQuery);


