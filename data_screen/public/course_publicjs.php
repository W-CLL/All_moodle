<script>
    var courseID = window.location.search.slice(1).split('=')[1];
    var role = $("#rolelist li.active").val();
    $("body").on('click','[data-stopPropagation]',function (e) {
        e.stopPropagation();
    });
    $('#rolelist li').click(function(){
        var tag  = $(this);
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': 'block_data_screen_set_role',
            'args': {
                'role': tag.val()
            }
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
                if(typeof result[0].data.status != 'undefined'){
                    tag.addClass('active').siblings().removeClass('active');
                    getRoleAndSidebar();
                }else{
                }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    });
    function getSidebar(methodname,semesterObj){
        var leftHtml = '';
        for (var i = 0; i < semesterObj.length; i++) {
            var item = semesterObj[i], course_list = item.course_list, itemHtml = '';
            var parentli_class = 'nav-item', arrow_class = 'arrow', li_class = 'nav-item';
            if (course_list.length >= 0) {
                for (var j = 0; j < course_list.length; j++) {
                    var itemJ = course_list[j];
                    if(methodname!='block_data_screen_search_courses'){
                        if(itemJ.id==courseID){
                            li_class = 'nav-item active';
                            parentli_class = 'nav-item active';
                            arrow_class = 'arrow open';
                        }else{
                            li_class = 'nav-item';
                        }
                    }else{
                        li_class = 'nav-item';
                        parentli_class = 'nav-item active open';
                        arrow_class = 'arrow open';
                    }
                    itemHtml += "<li class=\"" + li_class + "\">\n" +
                        "<a href=\"course_detail.php?id=" + itemJ.id + "\" class=\"nav-link \">\n" +
                        "<span class=\"title ml-20\" >" + itemJ.fullname + "</span>\n" +
                        "</a>\n" +
                        "</li>\n";
                }
                itemHtml = "<li class=\"" + parentli_class + "\">\n" +
                    "<a href=\"javascript:;\" class=\"nav-link nav-toggle\">\n" +
                    "<i class=\"customicon customicon-20 customicon-semester\"></i>\n" +
                    "<span class=\"title\">" + item.semester + "</span>\n" +
                    "<span class=\"arrow\"></span>\n" +
                    "</a>\n" +
                    "<ul class=\"sub-menu\">\n" + itemHtml + "</ul>\n" +
                    "</li>";
            }
            leftHtml = leftHtml + itemHtml;
        }
        $('.teaSituNavUl').html(leftHtml);
    }
    function getRoleAndSidebar(methodname,args){
        var data = Array();
        data[0] = {
            'index': 0,
            'methodname': methodname,
            'args': args
        };
        $.ajax({
            type : "POST",
            contentType: "application/json;",
            url : "<?PHP echo $CFG->wwwroot . '/lib/ajax/service.php?sesskey=' . $USER->sesskey;?>",
            data : JSON.stringify(data),
            success : function(result) {
              if (typeof result[0].data != 'undefined' && result[0].data.length != 0) {
                  var semesterObj = result[0].data;
                  getSidebar(methodname,semesterObj)
              }
            },
            error : function(e){
                alert("<?PHP echo get_string('network_error', 'block_data_screen');?>");
            }
        });
    }
    getRoleAndSidebar('block_data_screen_get_semester',{'role': role,'user': '<?PHP echo $USER->id;?>'});

    $(document).keydown(function(event){
        if(event.keyCode == 13){ $('.imgSearch').click(); }
    });
    $('.imgSearch').click(function(){
        var nameText = $('#search').val();
        console.log(nameText)
        if(nameText !== ''){
            getRoleAndSidebar('block_data_screen_search_courses',{'name': nameText,'role': role,'user': '<?PHP echo $USER->id;?>'});
        }else{
            window.location.reload();
        }
    });
</script>