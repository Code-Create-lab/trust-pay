@if (admin_permission_by_name("admin.commission.update"))
    <div id="commission-edit" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __("Edit Currency") }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('admin.commission.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method("PUT")
                    @include('admin.components.form.hidden-input',[
                        'name'          => 'target',
                        'value'         => old('target'),
                    ])
                    <div class="row mb-10-none">
                        <div class="col-xl-6 col-lg-6 form-group">
                            @include('admin.components.form.input',[
                                  'label'         => __('name').'*',
                                'name'          => 'commission_name',
                                'value'         => old('commission_name'),
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 form-group">
                            @include('admin.components.form.input',[
                                'label'         => __('Code').'*',
                                'name'          => 'commission_code',
                                'value'         => old('commission_code'),
                            ])
                        </div>
                        <div class="col-xl-3 col-lg-3 form-group">
                            @include('admin.components.form.input',[
                                 'label'         => __('Symbol').'*',
                                'name'          => 'commission_symbol',
                                'value'         => old('commission_symbol'),
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __("Cancel") }}</button>
                            <button type="submit" class="btn btn--base">{{ __("update") }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push("script")
        <script>
            $(document).ready(function(){
                reloadAllCountries("select[name=commission_country]");
                openModalWhenError("commission_edit","#commission-edit");
                $(document).on("click",".edit-modal-button",function(){
                    var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
                    var editModal = $("#commission-edit");

                    var readOnly = true;
                    if(oldData.type == "CRYPTO") {
                        readOnly = false;
                    }

                    editModal.find(".invalid-feedback").remove();
                    editModal.find(".form--control").removeClass("is-invalid");

                    editModal.find("form").first().find("input[name=target]").val(oldData.code);
                    editModal.find("input[name=commission_code]").val(oldData.code).prop("readonly",readOnly);
                    editModal.find("input[name=commission_name]").val(oldData.name).prop("readonly",readOnly);
                    editModal.find("input[name=commission_symbol]").val(oldData.symbol).prop("readonly",readOnly);
                    editModal.find("input[name=commission_rate]").val(oldData.rate);
                    editModal.find("input[name=commission_type]").val(oldData.type);
                    editModal.find("input[name=commission_flag]").attr("data-preview-name",oldData.flag);
                    editModal.find("input[name=commission_option]").val(oldData.option);
                    editModal.find(".selcted-commission-edit").text(oldData.code);
                    editModal.find("select[name=commission_country]").attr("data-old",oldData.country);

                    selectFormRadio("#commission-edit input[name=commission_role]",oldData.role);
                    reloadAllCountries("select[name=commission_country]");
                    fileHolderPreviewReInit("#commission-edit input[name=commission_flag]");
                    refreshSwitchers("#commission-edit");
                    openModalBySelector("#commission-edit");

                });
            });
        </script>
    @endpush
@endif
