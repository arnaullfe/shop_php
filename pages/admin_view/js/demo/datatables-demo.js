// Call the dataTables jQuery plugin
$(document).ready(function () {
    $('#dataTable').DataTable({
        language: {
            processing: "Processant...",
            search: "Cercar&nbsp;:",
            lengthMenu: "Mostra _MENU_ elements",
            info: "Mostrant _START_ a _END_ de _TOTAL_ entrades",
            infoEmpty: "La taula esta buida",
            infoFiltered: "(S'ha filtrat de _MAX_ entrades totals)",
            infoPostFix: "",
            loadingRecords: "Carregant...",
            zeroRecords:    "Hi ha 0 entrades",
            emptyTable:     "La taula esta buida",
             paginate: {
                first:      "Primera",
                previous:   "Anterior",
                next:       "Següent",
                last:       "Última"
              },
              aria: {
                sortAscending:  "Ordenar de manera ascendent",
                sortDescending: "Ordenar de manera descendent"
              }
        }
    });
});
