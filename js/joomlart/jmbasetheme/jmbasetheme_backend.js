//Base theme script
(function ($) {

    var $profileHandle = $("#wavethemes_jmbasetheme_jmbasethemegeneral_profile");
    var active_profile = "default";
    var skin_url = baseUrl + "skin/frontend/" + designPackage + "/" + defaultTheme + "/wavethemes/jmbasetheme/profiles";
    skin_url = skin_url.replace("admin/", "").replace("index.php/", "");

    $(document).ready(function ($) {
        //enable/disable the save button
        //in demo settings section
        if ($('#wavethemes_demo_demo-head').length){
            $('#wavethemes_demo_demo-head').trigger('click');
            $("button.save").addClass('disabled').prop('disabled', true);
        }

        //other case
        var isChecked = $('#wavethemes_jmbasetheme_jmbasethemegeneral_profile_inherit').prop("checked");
        $('#wavethemes_jmbasetheme_jmbasethemegeneral_profile_inherit').on('change', function(){
            if (this.checked) {
                if (isChecked){
                    $("button.save").addClass('disabled').prop('disabled', true);
                }
            } else {
                $("button.save").removeClass('disabled').prop('disabled', false);
            }
        });
        $('#wavethemes_jmbasetheme_jmbasethemegeneral_profile_inherit').trigger('change');

        //Remove use-default not used
        $('td.use-default').each(function (i) {
            var $childInput = $(this).find("input");
            if ($childInput.length) {
                if ($childInput.attr('id') !== 'wavethemes_jmbasetheme_jmbasethemegeneral_profile_inherit') {
                    if ($childInput.attr('id') !== 'wavethemes_jmbasetheme_jmbasethemegeneral_profilejson_inherit') {
                        if ($childInput.prop("checked")) {
                            $childInput.trigger('click');
                            $childInput.prop("checked", false);
                        }
                    }
                    $(this).hide();
                } else {
                    $childInput.on("change", function () {
                        $("#wavethemes_jmbasetheme_jmbasethemegeneral_profilejson_inherit").trigger("click");
                    });
                    if (!$childInput.prop("checked")) {
                        $childInput.trigger("change");
                    }
                }
            }
        });

        //off browser files on click event for label of file input
        $('input:file[class="input-file"]').each(function () {
            var $fileLabel = $(this).parent().siblings("td.label").children('label');
            if ($fileLabel.length) {
                $fileLabel.attr("for", "");
            }
        });

        //add the class for the title
        $('tr[id*="row_wavethemes_jmbasetheme_jmbasethemebase_title"],tr[id*="row_wavethemes_jmbasetheme_jmbasethemedevice_title"],tr[id*="row_wavethemes_jmbasetheme_jmbasethememobile_title"]').addClass("row_wavethemes_jmbasetheme_wavethemes_jmbasetheme_title").each(function (index, item) {
            $(item).children('td[class="scope-label"]').html("&nbsp;");
        });
        $("fieldset#wavethemes_jmbasetheme_jmbasethemegeneral").css({"display": "block"});

        //Bind event to save button
        $("button.save").attr("onclick", "");
        $(document).delegate("button.save", "click", function (e) {
            beforeSaveProfile(e);
        });

        //Event to delete image from check box
        $(document).delegate("span.delete-image input[type='checkbox']", "click", function (e) {
            if (!$("#deleteimages").length) {
                $(this).parent().after("<input type='hidden' name='groups[jmbasethemebase][fields][deleteimages][value]' id='deleteimages' value='' />");
            }
            if ($(this).prop("checked")) {
                if ($("#deleteimages").val() == "") {
                    $("#deleteimages").val($(this).val() + ",");
                } else {
                    $("#deleteimages").val($("#deleteimages").val() + $(this).val() + ",");
                }
                $(this).val("");
                $(this).parent().siblings("input[type='file']").val("");
            } else {
                $(this).val($(this).siblings("input[type='hidden']").val());
                $("#deleteimages").val($("#deleteimages").val().replace($(this).val() + ",", ""));
            }
        });

        $(document).delegate(".input-file", "change", function (e) {
            if ($("#" + $(this).attr("id") + "_delete").length) {
                if ($(this).val() !== "") {
                    $("#" + $(this).attr("id") + "_delete").attr("name", "delete_" + $(this).attr("id"));
                } else {
                    $("#" + $(this).attr("id") + "_delete").attr("name", $(this).attr("name") + "[delete]");
                }
            }
        });

        //change the active profile
        $profileHandle.on("change", function () {
            var nextprofile = $(this).val();
            $("span.delete-image input[type='checkbox']").prop("checked", false);
            if (!$("#deleteimages").length) {
                $("#deleteimages").val("");
            }

            $(".section-config").each(function (index, config) {
                var $fieldset = $(config).children("fieldset");
                if ($fieldset.attr("id").indexOf("jmbasetheme" + nextprofile) < 0 && $fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethemegeneral" && $fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethemebase" && $fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethemedevice" && $fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethememobile") {
                    $(config).hide();
                } else {
                    $(config).show();
                }
            });

            fillData($(this).val(), ["jmbasethemebase", "jmbasethemedevice", "jmbasethememobile", "jmbasetheme" + nextprofile]);

            //hide the restore button if this's not a core profile
            if (typeof profiles != 'undefined') {
                if (profiles[nextprofile]["iscore"]) {
                    $("button#restore-profile").css({"display": "block"});
                    $("button#delete-profile").css({"display": "none"});
                } else {
                    //$("span.delete-image").css({"display":"block"});
                    $("button#delete-profile").css({"display": "block"});
                    $("button#restore-profile").css({"display": "none"});
                }
            }

            if (typeof storeId == 'undefined') { // Default or Website scope
                if (!$("#wavethemes_jmbasetheme_jmbasethemegeneral_profile_inherit").length) { // no is website scope
                    $("#row_wavethemes_jmbasetheme_jmbasethemegeneral_profilejson").find('button').removeClass("disabled");
                }
            }

        });
        $profileHandle.trigger("change");

        //create new profile
        $("#create-profile").on("click", function () {
            createProfile();
        });

        //clone the active profile
        $("#clone-profile").on("click", function () {
            cloneProfile($profileHandle.val());
        });

        //restore core profiles to default values
        $("#restore-profile").on("click", function () {
            restoreProfile($profileHandle.val());
        });

        //delete non-core profiles
        $("#delete-profile").on("click", function () {
            beforeDeleteProfile($profileHandle.val());
        });
    });

    function getName(el, groups) {
        var matchs = '';
        groups.each(function (group) {
            if (matches = el.attr("name").match("groups\\[" + group + "\\]\\[fields\\]" + "\\[([^\\]]*)\\]" + "\\[value\\]")) {
                matchs = matches[1];
                return;
            }
        })
        return matchs;
    }

    function getCorrectFileName(fileName) {
        return fileName.replace(/[^a-z0-9_\-\.]+/gi, '_');
    }

    function rebuildData(group) {

        var els = serializeArray(group);
        var json = {};
        els.each(function (el) {

            var name = getName(el, group);
            var value = '', values = '';
            if (name != '') {
                value = el.val().toString().replace(/\n/g, '\\n').replace(/\t/g, '\\t').replace(/\r/g, '').replace(/&/g, 'amp;amp;amp;amp;');
                if (values = value.match(/[^\\]+\.(png|jpg|gif|bmp|jpeg|PNG|JPG|GIF|BMP)/)) {
                    value = values[0];
                }

//                if (el.hasClass("input-file") && value != "" && value.indexOf("default/") < 0) {
//                    value = getCorrectFileName(value);
//                    if (typeof storeId != "undefined") {
//                        value = "stores/" + storeId + "/" + value;
//                    } else {
//                        if (typeof websiteId != "undefined") {
//                            console.log(value);
//                            value = "websites/" + websiteId + "/" + value;
//                        } else {
//                            value = "default/" + value;
//                        }
//                    }
//                }
                if(el.hasClass("input-file"))
                {
                    if(value != "" && value.indexOf("default/") < 0)
                    {
                        value = getCorrectFileName(value);
                        if (typeof storeId != "undefined") {
                            value = "stores/" + storeId + "/" + value;
                        } else {
                            if (typeof websiteId != "undefined") {
                                value = "websites/" + websiteId + "/" + value;
                            } else {
                                value = "default/" + value;
                            }
                        }
                    }
                }

                json[name] = value;
            }

        }, this);
        return json;
    }

    function serializeArray(groups) {
        var els = new Array();
        var allelements = $("#config_edit_form")[0].elements;

        var k = 0;
        groups.each(function (group) {

            for (i = 0; i < allelements.length; i++) {
                var el = $(allelements[i]);
                if (el.attr("name") && ( el.attr("name").indexOf("groups[" + group + "]") >= 0 ) && (el.attr("type") !== "hidden" || el.attr("id") == "deleteimages")) {
                    els[k] = el;
                    k++;
                }
            }
        });
        return els;
    }

    function fillData(profile, groups) {
        active_profile = profile;
        var els = serializeArray(groups);
        if (els.length == 0) return;

        if (profiles[profile] == 'undefined') return;
        var cprofile = profiles[profile];
        var dprofile = profiles["default"];

        els.each(function (el) {

            var name = getName(el, groups);

            var ogrname = el.attr("name");

            var id = el.attr("id");
            var value = (cprofile[name] != 'undefined') ? cprofile[name] : ((dprofile && dprofile[name] != 'undefined') ? dprofile[name] : '');

            if (el.attr("type") == "file") {

                if ($("img#" + el.attr("id") + "_image").length) {
                    $("img#" + el.attr("id") + "_image").parent("a").remove();
                }

                if (value !== '' && value !== 'undefined') {

                    var $aimage = $('<a/>', {
                        "onclick": "imagePreview('" + id + "_image'); return false;",
                        "href": skin_url + "/" + active_profile + "/" + "images" + "/" + value
                    });

                    var $image = $('<img/>', {
                        "width": 22,
                        "height": 22,
                        "class": "small-image-preview v-middle",
                        "alt": value,
                        "title": value,
                        "id": el.attr("id") + "_image",
                        "src": skin_url + "/" + active_profile + "/" + "images" + "/" + value
                    });
                    $aimage.append($image)
                    el.before($aimage);
                    if (!el.next("span.delete-image").length) {
                        el.after('<span class="delete-image"><input type="checkbox" id="' + el.attr("id") + '_delete" class="checkbox" value="' + value + '" name="' + el.attr("name") + '[delete]" style="color: black;"><label for="' + el.attr("id") + '_delete" >' + DELETE_IMAGE_LABEL + '</label><input type="hidden" value="' + value + '" name="' + el.attr("name") + '[value]"></span>')
                    } else {
                        el.next("span.delete-image").show();
                    }
                } else if (el.next("span.delete-image").length > 0) {
                    el.next("span.delete-image").hide();
                }

                if (el.next("span.delete-image").length > 0) {
                    var spandeleteimg = el.next("span.delete-image");
                    el.remove();
                    spandeleteimg.before('<input type="file" class="input-file" value="' + value + '" name="' + ogrname + '" id="' + id + '">');
                }
            } else {
                el.val(value);
                $.fn.mColorPicker.setInputColor(el.attr("id"), value);
            }
        });
    }

    function createProfile() {

        var profilename = null, exits = false, msg = null, data = {};

        jPrompt(ENTER_PROFILE_NAME, '', null, function (r) {
            profilename = r;
            if (profilename == "") {
                jAlert(lg_please_enter_profile_name, null, function () {
                    createProfile();
                });
            } else if (profilename != null) {
                profilename = $.trim(profilename).replace(' ', '').toLowerCase();
                $("#wavethemes_jmbasetheme_jmbasethemegeneral_profile option").each(function () {
                    if ($.trim($(this).val()).toLowerCase() == profilename) {
                        exits = true;
                        var msg = PROFILE_NAME_EXIST.replace('%s', "<span class='profile-name'>" + profilename + "</span>");
                        jAlert(msg, null, function () {
                            createProfile();
                        });
                    }
                });

                if (profilename != null && !exits) {
                    var url = baseUrl + "jmbasetheme/index/createProfile";
                    data["profile"] = profilename;
                    data["settings"] = "{'iscore':false}";
                    if (typeof storeCode != 'undefined') {
                        data["store_code"] = storeCode;
                    }
                    submitForm(url, data);
                }
            }
        });
    }

    function cloneProfile(oldProfile) {

        var profileName = null, exits = false, msg = null, data = {}, url = '';

        jPrompt(ENTER_PROFILE_NAME, "copy-" + oldProfile, null, function (r) {
            profileName = r;
            if (profileName == "") {
                jAlert(ENTER_PROFILE_NAME, null, function () {
                    cloneProfile(oldProfile);
                });
            } else if (profileName != null) {
                profileName = $.trim(profileName).replace(' ', '').toLowerCase();
                $("#wavethemes_jmbasetheme_jmbasethemegeneral_profile option").each(function () {
                    if ($.trim($(this).val()).toLowerCase() == profileName) {
                        exits = true;
                        var msg = PROFILE_NAME_EXIST.replace('%s', "<span class='profile-name'>" + profileName + "</span>");
                        jAlert(msg, null, function () {
                            cloneProfile(oldProfile);
                        });
                    }
                });

                if (profileName != null && !exits) {
                    url = baseUrl + "jmbasetheme/index/cloneProfile";
                    data["old_profile"] = $profileHandle.val();
                    data["profile"] = profileName;
                    data["settings"] = rebuildData(["jmbasethemebase", "jmbasetheme" + oldProfile]);
                    if (typeof storeCode != 'undefined') {
                        data["store_code"] = storeCode;
                    }
                    if (typeof websiteCode != 'undefined') {
                        data["website_code"] = websiteCode;
                    }
                    submitForm(url, data);
                }
            }
        });
    }

    function restoreProfile(profileName) {
        var data = {};
        var url = baseUrl + "jmbasetheme/index/restoreProfile";
        data["profile"] = profileName;
        if (typeof storeCode != 'undefined') {
            data["store_code"] = storeCode;
        }
        if (typeof websiteCode != 'undefined') {
            data["website_code"] = websiteCode;
        }
        submitForm(url, data);
    }

    function deleteProfile(profileName) {
        var data = {};
        var url = baseUrl + "jmbasetheme/index/deleteProfile";
        data["profile"] = profileName;
        if (typeof storeCode !== 'undefined') {
            data["store_code"] = storeCode;
        }
        if (typeof websiteCode !== 'undefined') {
            data["website_code"] = websiteCode;
        }
        submitForm(url, data);
    }

    function saveProfile() {
        //disable file input empty
        //$('input:file[value=""]').attr('disabled', true);

        var profileName = $profileHandle.val();
        profileName = $.trim(profileName).replace(' ', '').toLowerCase();
        var data = {};
        var url = baseUrl + "jmbasetheme/index/saveProfile";
        data["profile"] = profileName;
        if (typeof storeCode !== 'undefined') {
            data["store_code"] = storeCode;
        }
        if (typeof websiteCode !== 'undefined') {
            data["website_code"] = websiteCode;
        }
        data["settings"] = rebuildData(["jmbasethemebase", "jmbasethemedevice", "jmbasethememobile", "jmbasetheme" + profileName]);

        //submit form
        submitForm(url, data);
    }

    function beforeSaveProfile(e) {
        var profileName = $profileHandle.val();
        profileName = $.trim(profileName).replace(' ', '').toLowerCase();
        var isChecked = $('#wavethemes_jmbasetheme_jmbasethemegeneral_profile_inherit').prop("checked");
        if ( !isChecked && isAssigned(profileName)) {
            jConfirm(PROFILE_USED_BY_OTHER_STORE.replace("%s", "<span class='profile-name'>" + profileName + "</span>"), null, function (r) {
                if (r) {
                    saveProfile();
                }
//				else{
//					jConfirm(DO_YOU_WANT_TO_CLONE_THIS_PROFILE, null, function(rs){
//						if(rs){
//							cloneProfile(profileName);
//						}
//					});
//				}
            });
        } else {
            e.preventDefault();
            saveProfile();
        }
    }

    function beforeDeleteProfile(profileName) {
        jConfirm(CONFIRM_DELETE_PROFILE, null, function (r) {
            if (r) {
                deleteProfile(profileName);
            }
        });
    }

    function isAssigned(profile) {
        var rs = false;
        if (typeof assignedProfiles != 'undefined') {

            $.each(assignedProfiles, function (sId, pr) {
                //console.log(pr);
                if (typeof storeId != 'undefined') { // is store scope
                    if (parseInt(sId) != parseInt(storeId) && pr == profile) {
                        rs = true;
                    }
                } else {  // not is store scope
                    if (pr == profile) {
                        rs = true;
                    }
                }
            });

        }

        return rs;
    }

    function addSectionConfig(oldProfile, profileName) {

        if ($("#wavethemes_jmbasetheme_jmbasetheme" + oldProfile + "-head").length) {
            var $systemConfig = $("#wavethemes_jmbasetheme_jmbasetheme" + oldProfile + "-head").parents("div.section-config");
            var sectionHtml = $systemConfig.html();
            var oldReplace = "jmbasetheme" + oldProfile
            var newSectionHtml = strReplace(oldReplace, "jmbasetheme" + profileName, sectionHtml);
            newSectionHtml = newSectionHtml.replace("Fieldset.applyCollapse", " ");
            var $newSystemConfig = $('<div/>', {
                "class": "section-config",
                "style": "display:block"
            })
            $newSystemConfig.html(newSectionHtml);
            $newSystemConfig.find("a#wavethemes_jmbasetheme_jmbasetheme" + profileName + "-head").html("JM Basetheme: settings for " + profileName + " profile");
            $systemConfig.after($newSystemConfig);
        }
    }

    function submitForm(url, data) {

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: "json",
            async: true,
            beforeSend: function () {
                showLoadingMask();
            },
            success: function (result) {

                hideLoadingMask();
                if (result.error) {
                    jAlert(result.error);
                    return false;
                }

                if (result.message) {
                    jAlert(result.message);
                }
                if (result.profile) {

                    profiles[result.profile] = result.settings;
                    if (result.type == "clone") {
                        $profileHandle.append('<option value="' + result.profile + '" selected="selected">' + result.profile + '</option>');
                        var oldProfile = result.old_profile;
                        var profileName = result.profile;
                        addSectionConfig(oldProfile, profileName);
                        $profileHandle.trigger("change");
                    } else if (result.type == "saveProfile") {
                        configForm.submit();
                        showLoadingMask();

                    } else if (result.type == "new") {
                        $profileHandle.append('<option value="' + result.profile + '" selected="selected">' + result.profile + '</option>');
                        addSectionConfig("core", result.profile);
                        $profileHandle.trigger("change");
                    } else if (result.type == "restore") {
                        profiles[result.profile] = result.settings;
                        $profileHandle.trigger("change");
                    } else if (result.type == "delete") {
                        $profileHandle.find("[value='" + result.profile + "']").remove();
                        $profileHandle.val("default");
                        $profileHandle.trigger("change");
                    }
                }
            }
        });
    }

    function showLoadingMask() {
        $("#loading-mask").css({"width": $(window).width() + "px", "height": $(window).height() + "px", "top": "0px", "z-index": "9999"});
        $("#loading-mask").show();

        //Turn off browser scroll
        $("body").css("overflow", "hidden");
    }

    function hideLoadingMask() {
        $("#loading-mask").css({"width": "auto", "height": "auto", "top": "auto"});
        $("#loading-mask").hide();

        //Turn on browser scroll
        $("body").css("overflow", "auto");
    }


    function strReplace(search, replace, subject, count) {
        // http://kevin.vanzonneveld.net
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Gabriel Paderni
        // +   improved by: Philip Peterson
        // +   improved by: Simon Willison (http://simonwillison.net)
        // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // +   bugfixed by: Anton Ongson
        // +      input by: Onno Marsman
        // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +    tweaked by: Onno Marsman
        // +      input by: Brett Zamir (http://brett-zamir.me)
        // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   input by: Oleg Eremeev
        // +   improved by: Brett Zamir (http://brett-zamir.me)
        // +   bugfixed by: Oleg Eremeev
        // %          note 1: The count parameter must be passed as a string in order
        // %          note 1:  to find a global variable in which the result will be given
        // *     example 1: strReplace(' ', '.', 'Kevin van Zonneveld');
        // *     returns 1: 'Kevin.van.Zonneveld'
        // *     example 2: strReplace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
        // *     returns 2: 'hemmo, mars'
        var i = 0,
            j = 0,
            temp = '',
            repl = '',
            sl = 0,
            fl = 0,
            f = [].concat(search),
            r = [].concat(replace),
            s = subject,
            ra = Object.prototype.toString.call(r) === '[object Array]',
            sa = Object.prototype.toString.call(s) === '[object Array]';
        s = [].concat(s);
        if (count) {
            this.window[count] = 0;
        }

        for (i = 0, sl = s.length; i < sl; i++) {
            if (s[i] === '') {
                continue;
            }
            for (j = 0, fl = f.length; j < fl; j++) {
                temp = s[i] + '';
                repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                s[i] = (temp).split(f[j]).join(repl);
                if (count && s[i] !== temp) {
                    this.window[count] += (temp.length - s[i].length) / f[j].length;
                }
            }
        }
        return sa ? s : s[0];
    }

})(jQuery);