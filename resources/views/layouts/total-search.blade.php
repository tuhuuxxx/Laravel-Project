<div class="row"> 
  <div class="col-md-6 pull-right">
      @php
        $stringFormat = strtolower(str_replace(' ', '', $item));
       
      @endphp
      <label  class="col-sm-3 control-label">{{$item}}</label>
      <input type="text" class=" col-sm-9" name="<?=$stringFormat?>" id="input<?=$stringFormat?>" placeholder="{{ $item }}">
  </div>
</div>