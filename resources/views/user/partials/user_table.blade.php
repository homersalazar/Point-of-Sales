<div>
    <x-table
        id="userTable"
        :headers="['Name', 'Email', 'Role', 'Action']"
    >
    </x-table>
</div>

<script>
    const update_user = (id, name, email) => {
        // Open modal
        document.getElementById('update_user_modal').checked = true;

        // Fill inputs
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        const formData = $(this).serialize() + '&_method=PUT';

        // Handle form submit
        $("#updateUserForm").off("submit").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: `/user/update_user/${id}`,
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
                    let message = 'An error occurred while updating the user.';
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
        $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.data') }}",
            columns: [
                {data: 'name'},
                {data: 'email'},
                {data: 'role'},
                {data: 'action'}
            ]
        });
    });
</script>
