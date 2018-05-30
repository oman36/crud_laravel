<nav aria-label="Page navigation example">
    @php $pagination->setPath(request()->getUri() . '?' . http_build_query(request()->except('page'))); @endphp
    <ul class="pagination justify-content-center">
        @if($pagination->previousPageUrl())
        <li class="page-item">
            <a class="page-link" href="{{$pagination->previousPageUrl()}}">Previous</a>
        </li>
        @endif
        <li class="page-item">
            <span class="page-link">{{$pagination->currentPage()}}</span>
        </li>
        @if($pagination->hasMorePages())
        <li class="page-item">
            <a class="page-link" href="{{$pagination->nextPageUrl()}}">Next</a>
        </li>
        @endif
    </ul>
</nav>