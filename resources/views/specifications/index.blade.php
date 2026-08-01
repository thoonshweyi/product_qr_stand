@extends("layouts.dashboard")

@section("content")
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                  <li class="inline-flex items-center">
                    <a href="#" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                      <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                      Home
                    </a>
                  </li>
                  <li>
                    <div class="flex items-center">
                      <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                      <a href="#" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Specifications</a>
                    </div>
                  </li>
                  <li>
                    <div class="flex items-center">
                      <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                      <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">List</span>
                    </div>
                  </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">All specifications</h1>
    </div>
</div>

<div class="bg-white dark:bg-gray-800">
    <div class="border-b border-gray-200 p-4 dark:border-gray-700">
        <form action="{{ route('specifications.index') }}" method="GET" class="grid w-full max-w-4xl grid-cols-12 items-end gap-3">
            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                <label for="specification-keyword" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Specification name</label>
                <input type="search" name="keyword" id="specification-keyword" value="{{ request('keyword') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter code or name">
            </div>

            <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                <label for="specification-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status_id" id="specification-status"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) request('status_id') === (string) $status->id)>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="col-span-12 flex gap-2 lg:col-span-4">
                <button type="submit" class="rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800">Search</button>
                @if (request()->filled('keyword') || request()->filled('status_id'))
                    <a href="{{ route('specifications.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="freeze-table-header" style="--freeze-table-max-height: calc(100vh - 18rem);">
        <table id="specificationstable" class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="checkbox-all" aria-describedby="checkbox-1" type="checkbox" class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            <label for="checkbox-all" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        No.
                    </th>
                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        Name
                    </th>
                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        Category
                    </th>
                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        Status
                    </th>
                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        User
                    </th>
                        <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        Created At
                    </th>
                    <!-- <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        Actions
                    </th> -->
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                
            </tbody>
        </table>
    </div>

    @if ($specifications->hasPages())
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">{{ $specifications->links() }}</div>
    @endif
</div>

@endsection
@section("css")
     <style>
        
     </style>
@endsection

@section("scripts")

    <script type="text/javascript">
        $('#branch_id').select2({
            placeholder: 'Choose Branch',
            width: '100%'
        });

        $('#branch_ids').select2({
            placeholder: 'Choose Other Branches',
            width: '100%'
        });

        $('#category_ids').select2({
            placeholder: 'Choose a Category',
            width: '100%'
        });



         // Start Passing Header Token
        // $.ajaxSetup({
        //     headers:{
        //             "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
        //     }
        // });
        // End Passing Header Token

        // Start Fetch All Datas 
        async function fetchalldatas(query=""){
            await $.ajax({
                url:"{{'/specifications'}}",
                method:"GET",
                data:{"query":query},
                dataType:"json",
                success:function(response){
                    console.log(response); // {status: 'scuccess', data: Array(2)}
                    
                    // $(".loading").hide();
                    $("#specificationstable tbody").empty();
                    
                    const datas = response.data;
                    // console.log(datas);
                    
                    let html;
                    datas.forEach(function(data,idx){
                        console.log(data); 
                        html += `
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-{{-- .id --}}" aria-describedby="checkbox-1" type="checkbox" class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-{{-- .id --}}" class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">${++idx}</td>
                            <td class="flex items-center p-4 mr-12 space-x-6 whitespace-nowrap">
                                <!-- <img class="w-10 h-10 rounded-full" src="/images/specifications/{{-- .avatar --}}" alt="{{-- .name --}} avatar"> -->
                                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">${ (data.name|| '').substring(0, 50) }</div>
                                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400">{{-- .email --}}</div>
                                </div>
                            </td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">${data?.category?.name ?? ''}</td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input
                                        type="checkbox"
                                        class="peer sr-only change-btn"
                                        ${ data.status_id === 3 ? "checked" : "" }
                                        data-id="${ data.id }"
                                    />
                                    <div
                                        class="h-5 w-9 rounded-full bg-gray-300 transition-colors
                                            after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4
                                            after:rounded-full after:bg-white after:transition-transform
                                            peer-checked:bg-blue-600 peer-checked:after:translate-x-4">
                                    </div>
                                </label>
                            </td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">${ data.user.name }</td>
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">${ data.created_at }</td>
                        </tr>
                        `;

                    });

                    // // $("#specificationstable tbody").html(html);
                    $("#specificationstable tbody").prepend(html);
                }
            });
        }
        fetchalldatas();
        // End Fetch All Datas



        //Start change-btn
        $(document).on("change",".change-btn",function(){

            var getid = $(this).data("id");
            // console.log(getid); // 1 2

            var setstatus = $(this).prop("checked") === true ? 1 : 2;
            // console.log(setstatus); // 3 4

            $.ajax({
                    url:"specificationsstatus",
                    type:"GET",
                    dataType:"json",
                    data:{"id":getid,"status_id":setstatus},
                    success:function(response){
                        console.log(response); // {success: 'Status Change Successfully'}
                        console.log(response.success); // Status Change Successfully
                    
                        Swal.fire({
                            title: "Updated!",
                            text: "Updated Successfully",
                            icon: "success"
                        });
                    }
            });
        });
        // End change btn
        
    </script>
    
@endsection