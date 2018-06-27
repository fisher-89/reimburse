@extends('public.layout')

@section('header_right')
    {'show':true,'control':true,'text':'刷新','onSuccess':function(){location.reload();}}
@endsection
@section('title')
    @if(request()->id)编辑收款人资料@else 添加收款人资料 @endif
@endsection
@section('content')
    <div class='new_report'>
        <form action="{{asset('payee_save')}}" method="post" id="form">
            {{csrf_field()}}
            <div>
                <p><i class="fa fa-phone"></i> 收款人手机(<span class="text-danger">必填</span>)</p>
                <input type="text" name="phone" placeholder="请输入手机号码" limit="11,11" maxlength="11"
                       value="{{$user['phone'] or ''}}" data_type="num" required>
            </div>
            <div>
                <p><i class="fa fa-file"></i> 银行类型(<span class="text-danger">必填</span>)</p>
                <select class="form-control" id="bank_other" name="bank_other" required
                        onchange="bankOtherChange(this.value)">
                    @foreach(\App\Models\Bank::all() as $bank)
                        <option value="{{$bank->name}}"
                                @if($user['bank_other'] == $bank->name) selected @endif>{{$bank->name}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <p><i class="fa fa-user"></i> 开户名(<span class="text-danger">必填</span>)</p>
                <input type="text" name="bank_account_name" placeholder="如：张三" limit="2,20" maxlength="20" minlength="2"
                       value="{{$user['bank_account_name'] or ''}}" required>
            </div>
            <div>
                <p><i class="fa fa-pencil"></i> 银行卡号(<span class="text-danger">必填</span>)</p>
                <input type="text" name="bank_account" placeholder="请输入银行卡号如622******888" limit="9,22"
                       value="{{$user['bank_account'] or ''}}" maxlength="20" required data_type="num">
            </div>
            <div>
                <p><i class="fa fa-flag"></i> 开户行所在省(<span class="text-danger">必填</span>)</p>
                <select class="form-control" id="province_of_account" name="province_of_account" required
                        onchange="region.provinceChange(this.value)">
                </select>
            </div>
            <div>
                <p><i class="fa fa-flag"></i> 开户行所在市（选填）</p>
                <select class="form-control" id="city_of_account" name="city_of_account">
                </select>
            </div>
            <div id="bank_dot">
                <p><i class="fa fa-flag"></i> 开户网点（<span class="text-danger">必填</span>）</p>
                <input type="text" name="bank_dot" placeholder="请填写开户网点" maxlength="30"
                       value="{{$user['bank_dot'] or ''}}">
            </div>
            <input type="hidden" name="id" value="{{$user['id'] or ''}}" />
        </form>
    </div>
@endsection

@section('footer')
    <div class='new_report'>
        <button class="btn btn-lg btn-success submit-btn" onclick="save()" style="width:100%"><i class="fa fa-save"></i>
            保存
        </button>
        <div class="clearfix"></div>
    </div>
@endsection
@section('js')
    @parent
    <script type="text/javascript" src="{{asset('js/reimburse/region.js')}}"></script>
    <script>
      $(function () {
        //初始获取省市区数据
        region.getRegion();
        bankDotInit();//初始开户网点

      });

      //地区处理
      var region = {
        regionData: new regionClass(),  //实列地区类
        updateProvinceId: '{{$user->province_of_account or 510000}}',//编辑时省id 或默认24四川省
        updateCityId: '{{$user->city_of_account or ''}}',//编辑时的市id

        //获取省市数据
        getRegion: function () {
          var province_id = this.getProvinceSelectOption();//省的option 返回省id
          this.getCitySelectOption(province_id);
        },

        //获取省option数据
        getProvinceSelectOption: function () {
          var province = this.regionData.province();
          var option_str = '';
          $.each(province, function (k, v) {
            var selected = (v.id == region.updateProvinceId) ? 'selected' : '';
            option_str += '<option value="' + v.id + '" ' + selected + '>' + v.region_name + '</option>';
          });
          $('#province_of_account').html(option_str);
          return $('#province_of_account').val();
        },

        //获取市option数据  province_id 省id
        getCitySelectOption: function (province_id) {
          var city = this.regionData.city();
          var html = '';
          html = '<option value="">--请选择--</option>';
          $.each(city, function (k, v) {
            if (v.parent_id == province_id) {
              var selected = (v.id == region.updateCityId) ? "selected" : '';
              html += '<option value="' + v.id + '" ' + selected + ' >' + v.region_name + '</option>'
            }
          });
          $('#city_of_account').html(html);
        },

        //change 省
        provinceChange: function (province_id) {
          this.getCitySelectOption(province_id);
        },
      };


      function save() {
        $(".waiting").show();
        var url = $("#form").attr("action");
        var data = $("#form").serialize();
        var type = $("#form").attr('method');
        $.ajax({
          type: type,
          url: url,
          data: data,
          success: function (info) {
            if (info === "success") {
              window.history.go(-1);
            } else {
              $(".waiting").hide();
              alert("保存失败,返回值：" + info);
            }
          },
          error: function (err) {
            $(".waiting").hide();
            if (err.status === 422) {
              var responses = JSON.parse(err.responseText);
              for (var i in responses) {
                alert(responses[i]);
              }

            } else {
              document.write(err.responseText);
            }
          }
        });
      }


      /**
       * 初始开户网点
       */
      function bankDotInit() {
        var value = $('#bank_other').val();
        bankOtherChange(value);
      }

      /**
       * 银行类型change
       */
      function bankOtherChange(value) {
        if (value !== "中国农业银行") {
          $('#bank_dot').show();
          $('#bank_dot input').attr('required', 'required');
        } else {
          $('#bank_dot').hide();
          $('#bank_dot input').removeAttr('required');
          $('#bank_dot input').val('');
        }
        checkForm();
      }

    </script>
@stop
