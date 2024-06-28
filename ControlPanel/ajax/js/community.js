const feedbackTxt = document.querySelector('.feedback');

function communityTable() {
    $.ajax({
        url: '../ajax/php/community.php',
        type: 'POST',
        data: { action: 'communityTable' },
        success: function(data) {
            if ($.fn.DataTable.isDataTable('#communityTable')) {
                $('#communityTable').DataTable().destroy();
            }

            $('#communityTable').DataTable({
                paging: true,
                searching: true,
                data: JSON.parse(data),
                columns: [
                    { data: 'code' },
                    { data: 'email' },
                    { data: 'created_at' },
                    {
                        title: 'Status',
                        render: function(data, type, row) {
                            return 'Completed'
                        }
                    }
                ]
            });
        }
    });
}


function contactTable() {
    $.ajax({
        url: '../ajax/php/community.php',
        type: 'POST',
        data: { action: 'contactTable' },
        success: function(data) {
            if ($.fn.DataTable.isDataTable('#contactTable')) {
                $('#contactTable').DataTable().destroy();
            }

            $('#contactTable').DataTable({
                paging: true,
                searching: true,
                data: JSON.parse(data),
                columns: [
                    { data: 'code' },
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'phone' },
                    { data: 'address' },
                    {
                        title: 'Note',
                        render: function(data, type, row) {
                            if (row.note.length > 15) {
                                return row.note.substring(0, 15) + "...";
                            } else {
                                return row.note;
                            }
                        }
                    },
                    { data: 'date' }
                ]
            });
        }
    });
}


$(document).ready(function() {
    communityTable();
    contactTable();
});