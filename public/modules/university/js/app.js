
$(document).ready(function() {
    // get department data
    $("#select_faculty").on("change", function() {

        var url = $("#url").val();
        var un_faculty_id = $(this).val();
        var i = 0;
        var formData = {
            un_faculty_id : un_faculty_id,
        };
      

        // get department from faculty
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-dept",

            beforeSend: function() {
                $('#select_dept_loader').addClass('pre_loader').removeClass('loader');
              
            },
            success: function(data) {
                // console.log(data);
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#select_dept").find("option").not(":first").remove();
                        $("#select_dept_div ul").find("li").not(":first").remove();

                        $.each(item, function(i, dept) {
                            $("#select_dept").append(
                                $("<option>", {
                                    value: dept.id,
                                    text: dept.name,
                                })
                            );

                            $("#select_dept_div ul").append(
                                "<li data-value='" +
                                dept.id +
                                "' class='option'>" +
                                dept.name +
                                "</li>"
                            );
                        });
                    } else {
                        $("#select_dept_div .current").html("SELECT Department ");
                        $("#select_dept").find("option").not(":first").remove();
                        $("#select_dept_div ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_dept_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
    });

    //get department subject 
    $("#select_dept").on("change", function() {
    
        var url = $("#url").val();
        var un_department_id = $(this).val();
        var i = 0;
        var formData = {
            un_department_id : un_department_id,
        };
      

        // get semester label subject
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-subject-from-dept",
            beforeSend: function() {
                $('#select_semester_label_subject_loader').addClass('pre_loader').removeClass('loader');
                
            },
            success: function(data) {
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#select_semester_label_subject").find("option").not(":first").remove();
                        $("#select_semester_label_subject_div ul").find("li").not(":first").remove();

                        $.each(item, function(i, subject) {
                            $("#select_semester_label_subject").append(
                                $("<option>", {
                                    value: subject.id,
                                    text: subject.subject_name,
                                })
                            );

                            $("#select_semester_label_subject_div ul").append(
                                "<li data-value='" +
                                subject.id +
                                "' class='option'>" +
                                subject.subject_name +
                                "</li>"
                            );
                        });
                    } else {
                        $("#select_semester_label_subject_div .current").html("SELECT SEMESTER LEVEL *");
                        $("#select_semester_label_subject").find("option").not(":first").remove();
                        $("#select_semester_label_subject_div ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_semester_label_subject_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
        
    });
});

// get semester level 
$(document).ready(function() {
    $("#select_semester").on("change", function() {
        
        var url = $("#url").val();
        var i = 0;
        let semester_id = $(this).val();
        let academic_id = $('#select_academic').val();  
        let session_id = $('#select_session').val();
        let faculty_id = $('#select_faculty').val();
        let department_id = $('#select_dept').val();
        let subject_id = $('#select_semester_label_subject').val();

       

        if (session_id =='') {
            setTimeout(function() {
                toastr.error(
                "Session Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500); 
            $("#select_semester").val('').niceSelect('update');          
            return ;
        }
        if (department_id =='') {
            setTimeout(function() {
                toastr.error(
                "Department Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            $("#select_semester").val('').niceSelect('update');
            return ;
        }
        if (semester_id =='') {
            setTimeout(function() {
                toastr.error(
                "Semester Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            $("#select_semester").val('').niceSelect('update');
            return ;
        }
        if (academic_id =='') {
            setTimeout(function() {
                toastr.error(
                "Academic Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            $("#select_semester").val('').niceSelect('update');
            return ;
        }



        var formData = {
            semester_id : semester_id,
            academic_id : academic_id,
            session_id : session_id,
            faculty_id : faculty_id,
            department_id : department_id,
            subject_id : subject_id,
        };
      

        // get department from faculty
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-semester-level",

            beforeSend: function() {
                $('#select_semester_label_loader').addClass('pre_loader').removeClass('loader');
              
            },
            success: function(data) {
                // console.log(data);
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#select_semester_label").find("option").not(":first").remove();
                        $("#select_semester_label_div ul").find("li").not(":first").remove();

                        $.each(item, function(i, dept) {
                            $("#select_semester_label").append(
                                $("<option>", {
                                    value: dept.id,
                                    text: dept.name,
                                })
                            );

                            $("#select_semester_label_div ul").append(
                                "<li data-value='" +
                                dept.id +
                                "' class='option'>" +
                                dept.name +
                                "</li>"
                            );
                        });
                    } else {
                        $("#select_semester_label_div .current").html("SELECT SEMESTER LEVEL *");
                        $("#select_semester_label").find("option").not(":first").remove();
                        $("#select_semester_label_div ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_semester_label_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
    });
});

//get semester label subject
$(document).ready(function() {
    $("#select_semester_label").on("change", function() {

        var url = $("#url").val();
        var i = 0;
        let un_semester_id = $(this).val();
        let un_academic_id = $('#select_academic').val();  
        let un_session_id = $('#select_session').val();
        let un_faculty_id = $('#select_faculty').val();
        let un_department_id = $('#select_dept').val();
        let un_semester_label_id = $('#select_semester_label').val();
        let is_multiple = $(this).data('is_multiple');

        // if (un_session_id =='') {
        //     setTimeout(function() {
        //         toastr.error(
        //         "Session Not Found",
        //         "Error ",{
        //             timeOut: 5000,
        //     });}, 500);
         
        //     $("#select_semester option:selected").prop("selected", false)
        //     return ;
        // }
        if (un_department_id =='') {
            setTimeout(function() {
                toastr.error(
                "Department Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            $("#select_semester option:selected").prop("selected", false)

            return ;
        }
        if (un_semester_id =='') {
            setTimeout(function() {
                toastr.error(
                "Semester Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            $("#select_semester option:selected").prop("selected", false)

            return ;
        }
        if (un_academic_id =='') {
            setTimeout(function() {
                toastr.error(
                "Academic Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            return ;
        }

        if (un_semester_label_id =='') {
            setTimeout(function() {
                toastr.error(
                "Semester Label Not Found",
                "Error ",{
                    timeOut: 5000,
            });}, 500);
            return ;
        }



        var formData = {
            semester_id : un_semester_id,
            academic_id : un_academic_id,
            session_id : un_session_id,
            faculty_id : un_faculty_id,
            department_id : un_department_id,
            un_semester_label_id : un_semester_label_id,
        };
      

        // get semester label subject
        // $.ajax({
        //     type: "GET",
        //     data: formData,
        //     dataType: "json",
        //     url: url + "/university/" + "get-semester-level-subject",
        //     beforeSend: function() {
        //         $('#select_semester_label_subject_loader').addClass('pre_loader').removeClass('loader');
              
        //     },
        //     success: function(data) {
        //         var a = "";
        //         $.each(data, function(i, item) {
        //             if (item.length) {
        //                 $("#select_semester_label_subject").find("option").not(":first").remove();
        //                 $("#select_semester_label_subject_div ul").find("li").not(":first").remove();

        //                 $.each(item, function(i, subject) {
        //                     $("#select_semester_label_subject").append(
        //                         $("<option>", {
        //                             value: subject.subject.id,
        //                             text: subject.subject.subject_name,
        //                         })
        //                     );

        //                     $("#select_semester_label_subject_div ul").append(
        //                         "<li data-value='" +
        //                         subject.subject.id +
        //                         "' class='option'>" +
        //                         subject.subject.subject_name +
        //                         "</li>"
        //                     );
        //                 });
        //             } else {
        //                 $("#select_semester_label_subject_div .current").html("SELECT SUBJECT *");
        //                 $("#select_semester_label_subject").find("option").not(":first").remove();
        //                 $("#select_semester_label_subject_div ul").find("li").not(":first").remove();
        //             }
        //         });
        //     },
        //     error: function(data) {
        //         console.log("Error:", data);
        //     },
        //     complete: function() {
        //         i--;
        //         if (i <= 0) {
        //             $('#select_semester_label_subject_loader').removeClass('pre_loader').addClass('loader');
        //         }
        //     }
        // });

        //Exam Subject
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-semester-level-subject",

            beforeSend: function() {
                $('#unSubjectLoader').addClass('pre_loader').removeClass('loader');
            },
            
            success: function(data) {
                $.each(data, function(i, item) {
                    $("#universityExamSubejct").empty();
                    var appendRow = "";
                    appendRow += "<div class='col-lg-12 mt-0'>";
                    $.each(item, function(i, subject) {
                        appendRow += "<div class='input-effect'>";
                        appendRow +=
                            "<input type='checkbox' id='universitySubjects_" +
                            subject.subject.id +
                            "' class='common-checkbox subject-checkbox' name='subjects_ids[]' value='" +
                            subject.subject.id +
                            "' onclick='dd(" +
                            subject.subject.id +
                            ")'>";
                        appendRow +=
                            "<label for='universitySubjects_" +
                            subject.subject.id +
                            "'>" +
                            subject.subject.subject_name +
                            "</label>";
                        appendRow += "</div>";
                    });
                    appendRow += "<div class='col-lg-12'>";
                    $("#universityExamSubejct").append(appendRow);
                });
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#unSubjectLoader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
        //get section 
        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-section",

            beforeSend: function() {
                $('#select_section_loader').addClass('pre_loader').removeClass('loader');
            },
     
                success: function(data) {
                    // console.log(data);
                    var a = "";
                    $.each(data, function(i, item) {
                        if (is_multiple ==1) {
                            if (item.length) {
                                $("#selectSectionss").find("option").remove();
                                $("#selectSectionsDiv ul").find("li").not(":first").remove();
                                $.each(item, function (i, section) {
                                    $("#selectSectionss").append(
                                        $("<option>", {
                                            value: section.id,
                                            text: section.section_name,
                                        })
                                    );                                    
                                });
                            } else {
                                $("#selectSectionsDiv .current").html("SELECT SECTION *");
                                $("#selectSectionss").find("option").not(":first").remove();
                                $("#selectSectionsDiv ul").find("li").not(":first").remove();
                            }
                        } else {
                            if (item.length) {
                                $("#select_section").find("option").not(":first").remove();
                                $("#select_section_div ul").find("li").not(":first").remove();

                                $.each(item, function(i, section) {
                                    $("#select_section").append(
                                        $("<option>", {
                                            value: section.id,
                                            text: section.section_name,
                                        })
                                    );

                                    $("#select_section_div ul").append(
                                        "<li data-value='" +
                                        section.id +
                                        "' class='option'>" +
                                        section.section_name +
                                        "</li>"
                                    );
                                });
                            } else {
                                $("#select_section_div .current").html("SELECT SECTION *");
                                $("#select_section").find("option").not(":first").remove();
                                $("#select_section_div ul").find("li").not(":first").remove();
                            }
                         }
                         try {
                            $('#selectSectionss').multiselect('reset');
                         } catch (error) {
                            
                         }
                         console.log($('#selectSectionss').val());
                    });
                },
       
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_section_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
        
    });
});
// 

// Exam Type
$(document).ready(function() {
    $("#select_semester_label").on("change", function() {

        var url = $("#url").val();
        var i = 0;
        let semester_id = $(this).val();
        let academic_id = $('#select_academic').val();  
        let session_id = $('#select_session').val();
        let faculty_id = $('#select_faculty').val();
        let department_id = $('#select_dept').val();
        let semester_label_id = $('#select_semester_label').val();

        var formData = {
            semester_id : semester_id,
            academic_id : academic_id,
            session_id : session_id,
            faculty_id : faculty_id,
            department_id : department_id,
            semester_label_id : semester_label_id,
        };
        //check select_section element is exist or not
        if ($('#select_section').length == 0) {

        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-exam-type",
            beforeSend: function() {
                $('#select_exam_type_loader').addClass('pre_loader').removeClass('loader');
            },
            success: function(data) {
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#select_exam_typ_subject").find("option").not(":first").remove();
                        $("#select_exam_typ_subject_div ul").find("li").not(":first").remove();

                        $.each(item, function(i, examTitle) {
                            $("#select_exam_typ_subject").append(
                                $("<option>", {
                                    value: examTitle.exam_type.id,
                                    text: examTitle.exam_type.title,
                                })
                            );

                            $("#select_exam_typ_subject_div ul").append(
                                "<li data-value='" +
                                examTitle.exam_type.id +
                                "' class='option'>" +
                                examTitle.exam_type.title +
                                "</li>"
                            );
                        });
                    } else {
                        $("#select_exam_typ_subject_div .current").html("SELECT EXAM *");
                        $("#select_exam_typ_subject").find("option").not(":first").remove();
                        $("#select_exam_typ_subject_div ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_exam_type_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
    }
    });
    $("#select_section").on("change", function() {

        var url = $("#url").val();
        var i = 0;
        let semester_id = $(this).val();
        let academic_id = $('#select_academic').val();  
        let session_id = $('#select_session').val();
        let faculty_id = $('#select_faculty').val();
        let department_id = $('#select_dept').val();
        let semester_label_id = $('#select_semester_label').val();
        let section_id = $('#select_section').val();

        var formData = {
            semester_id : semester_id,
            academic_id : academic_id,
            session_id : session_id,
            faculty_id : faculty_id,
            department_id : department_id,
            semester_label_id : semester_label_id,
            section_id : section_id,
            
        };

        $.ajax({
            type: "GET",
            data: formData,
            dataType: "json",
            url: url + "/university/" + "get-exam-type",
            beforeSend: function() {
                $('#select_exam_type_loader').addClass('pre_loader').removeClass('loader');
            },
            success: function(data) {
                var a = "";
                $.each(data, function(i, item) {
                    if (item.length) {
                        $("#select_exam_typ_subject").find("option").not(":first").remove();
                        $("#select_exam_typ_subject_div ul").find("li").not(":first").remove();

                        $.each(item, function(i, examTitle) {
                            $("#select_exam_typ_subject").append(
                                $("<option>", {
                                    value: examTitle.exam_type.id,
                                    text: examTitle.exam_type.title,
                                })
                            );

                            $("#select_exam_typ_subject_div ul").append(
                                "<li data-value='" +
                                examTitle.exam_type.id +
                                "' class='option'>" +
                                examTitle.exam_type.title +
                                "</li>"
                            );
                        });
                    } else {
                        $("#select_exam_typ_subject_div .current").html("SELECT EXAM *");
                        $("#select_exam_typ_subject").find("option").not(":first").remove();
                        $("#select_exam_typ_subject_div ul").find("li").not(":first").remove();
                    }
                });
            },
            error: function(data) {
                console.log("Error:", data);
            },
            complete: function() {
                i--;
                if (i <= 0) {
                    $('#select_exam_type_loader').removeClass('pre_loader').addClass('loader');
                }
            }
        });
    });
});

//Select Subject
    $(document).ready(function() {
        $("#select_exam_typ_subject").on("change", function() {

            var url = $("#url").val();
            var i = 0;
            let semester_id = $(this).val();
            let academic_id = $('#select_academic').val();  
            let session_id = $('#select_session').val();
            let faculty_id = $('#select_faculty').val();
            let department_id = $('#select_dept').val();
            let semester_label_id = $('#select_semester_label').val();
            let select_exam_typ_subject = $('#select_exam_typ_subject').val();

            var formData = {
                un_semester_id : semester_id,
                un_academic_id : academic_id,
                un_session_id : session_id,
                un_faculty_id : faculty_id,
                un_department_id : department_id,
                un_semester_label_id : semester_label_id,
                un_select_exam_typ_subject : select_exam_typ_subject,
            };

            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/university/" + "get-exam-type-subject",
                beforeSend: function() {
                    $('#select_exam_subject_loader').addClass('pre_loader').removeClass('loader');
                },
                success: function(data) {
                    var a = "";
                    $.each(data, function(i, item) {
                        if (item.length) {
                            $("#select_un_exam_type_subject").find("option").not(":first").remove();
                            $("#select_un_exam_type_subject_div ul").find("li").not(":first").remove();

                            $.each(item, function(i, examTitle) {
                                console.log(examTitle);
                                $("#select_un_exam_type_subject").append(
                                    $("<option>", {
                                        value: examTitle.subject_details.id,
                                        text: examTitle.subject_details.subject_name,
                                    })
                                );

                                $("#select_un_exam_type_subject_div ul").append(
                                    "<li data-value='" +
                                    examTitle.subject_details.id +
                                    "' class='option'>" +
                                    examTitle.subject_details.subject_name +
                                    "</li>"
                                );
                            });
                        } else {
                            $("#select_un_exam_type_subject_div .current").html("SELECT SUBJECT *");
                            $("#select_un_exam_type_subject").find("option").not(":first").remove();
                            $("#select_un_exam_type_subject_div ul").find("li").not(":first").remove();
                        }
                    });
                },
                error: function(data) {
                    console.log("Error:", data);
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $('#select_exam_subject_loader').removeClass('pre_loader').addClass('loader');
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        $("#studentSemesterWiseReport").on("change", function() {
            let i = 0;
            let url = $("#url").val();
            let student_id = $('#student_id').val();
            let semester_label_id = $(this).val();
            if(semester_label_id != ""){
                $('#dynamicExamReport').html(" ");
                $.ajax({
                    url: url + "/university/" + "get-semester-label-wise-report",
                    method: "GET",
                    data : { student_id : student_id, semester_label_id: semester_label_id},
                    beforeSend: function() {
                        $('#examReportLoader').addClass('pre_loader').removeClass('loader');
                    },
                    success: function(data) {
                        $('#dynamicExamReport').html(data);
                    },
                    error: function(data) {
                        console.log("Error:", data);
                    },
                    complete: function() {
                        i--;
                        if (i <= 0) {
                            $('#examReportLoader').removeClass('pre_loader').addClass('loader');
                        }
                    }
                });
            }
        });
    });