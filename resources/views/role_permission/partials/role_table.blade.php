<div>
    <x-table
        id="roleTable"
        :headers="['Name', 'Action']"
    >
    </x-table>
</div>

<script>
    const update_role = (id, name) => {
        // Open modal
        document.getElementById('update_role_modal').checked = true;

        // Fill inputs
        document.getElementById('name').value = name;

        // Handle form submit
        $("#updateRoleForm").off("submit").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: `/access/update_role/${id}`,
                method: "POST",
                data: formData,
                success: function(data){
                    Swal.fire({
                    title: data.status === 'success' ? 'Success!' : 'Info!',
                        text: data.message,
                        icon: data.status,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr){
                    let message = 'An error occurred while updating the role.';
                    if(xhr.responseJSON && xhr.responseJSON.message){
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }
            });
        });
    }

    $(document).ready(function(){
        $('#roleTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('roles.data') }}",
            columns: [
                {data: 'name'},
                {data: 'action'}
            ]
        });
    });
</script>
