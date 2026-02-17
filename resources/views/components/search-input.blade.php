@props([
    'placeholder' => '',
    'color' => 'ghost',
    'size' => 'sm',
    'url' => '',
    'target' => '' // id of the table or container to update
])

<div>
    <label class="input input-bordered input-{{ $size }} input-{{ $color }} flex items-center gap-2">
        <input
            name="search"
            type="text"
            class="grow w-full"
            placeholder="{{ $placeholder }}"
            id="searchInput"
        />
        <i class="fa-solid fa-magnifying-glass h-4 w-4 opacity-70"></i>
    </label>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let timeout = null;
        const searchInput = document.getElementById('searchInput');
        const target = document.getElementById('{{ $target }}');
        const perPageSelect = document.getElementById('perPageSelect');

        if (!target) return;

        // function fetchSearchOrFilter(url = null) {
        //     const perPage = perPageSelect ? perPageSelect.value : 10;
        //     const search = searchInput ? searchInput.value : '';
        //     const fetchUrl = url || `{{ $url }}?per_page=${perPage}&search=${search}`;

        //     fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        //         .then(res => res.text())
        //         .then(html => {
        //             target.innerHTML = html;
        //         })
        //         .catch(err => console.error(err));
        // }
        function fetchSearchOrFilter(url = null) {
            const perPage = perPageSelect ? perPageSelect.value : 10;
            const search = searchInput ? searchInput.value : '';

            // If clearing search, reset to first page
            let fetchUrl = url || `{{ $url }}?per_page=${perPage}&search=${search}`;
            if (!search) {
                fetchUrl = `{{ $url }}?per_page=${perPage}&search=`;
            }

            fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => target.innerHTML = html)
                .catch(err => console.error(err));
        }

        if (perPageSelect) {
            perPageSelect.addEventListener('change', () => fetchSearchOrFilter());
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(fetchSearchOrFilter, 500);
            });
        }

        // Handle pagination links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if(link) {
                e.preventDefault();
                fetchSearchOrFilter(link.href);
            }
        });
    });
</script>
