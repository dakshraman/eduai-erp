$("#livesearch").hide();
function resolveUrl(path) {
    if (!path) return '#';
    if (/^https?:\/\//i.test(path)) return path;
    var base = ($('#url').val() || window.location.origin).replace(/\/+$/, '');
    var suffix = String(path).replace(/^\/+/, '');
    return base + '/' + suffix;
}

function showResult(str) {
    var url = $('#url').val();
    var $results = $('#livesearch');
    if (str.length == 0) {
        $results.empty().hide();
        return;
    }


    $.ajax({
            method:'POST',
            url: url + '/' + 'search',
            data:{search:str},
            success:function(data){
                $results.empty().show();
                if (Array.isArray(data) && data.length != 0) {
                    data.forEach(value => {
                        if (!value.route) {
                            return;
                        }

                        $('<a>', {
                            href: resolveUrl(value.route),
                            text: value.name || '',
                            role: 'option'
                        }).appendTo($results);
                    });
                }

                if (!$results.children().length) {
                    $('<span>', {
                        class: 'search-no-result',
                        text: 'Not Found'
                    }).appendTo($results);
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }

        });

}
$(document).on("click", function(e) {
    if (!$(e.target).closest('.serach_field-area').length)  {
        $("#livesearch").hide();
    }
});


$("#liveStudentSearch").hide();
function showStudent(str) {
    var url = $('#url').val();
    if (str.length == 0) {
        document.getElementById("liveStudentSearch").innerHTML = "";
        $("#liveStudentSearch").hide();
        return;
    }
    $.ajax({
        method: 'POST',
        url: url + '/' + 'dashboard-student-search',
        data: {
            search: str
        },
            success: function (data) {
                $("#liveStudentSearch").show();
                if (data.length != 0) {
                    document.getElementById("liveStudentSearch").innerHTML = "";
                    data.forEach(value => {
                        $("#liveStudentSearch").append(`<a target="_blank" href="${resolveUrl(value.route)}">${value.name}</a>`);
                    });
                } else {
                    document.getElementById("liveStudentSearch").innerHTML = "";
                    $("#liveStudentSearch").append("<a id='lol'> Not Found </a>");
                }
            },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}
$(document).on("click", function (e) {
    if (!$(e.target).closest('.serach_field-area').length) {
        $("#liveStudentSearch").hide();
    }
});
