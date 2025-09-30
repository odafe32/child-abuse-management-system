  <form action="{{ route('logout') }}" method="POST" style="display: inline; cursor: pointer;">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="ri-logout-circle-line me-2"></i><span class="align-middle">Logout</span>
                                        </button>
                                    </form>
