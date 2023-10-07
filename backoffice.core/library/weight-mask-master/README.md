## jQuery Weight Mask Plugin

## Full documentation with showcases
Full documentation available on [http://www.smartsource.pl/weight-mask](http://www.smartsource.pl/weight-mask)

## Compatible with:
* iOS 
* Android 
* Chrome 
* Firefox 
* Safari 
* Internet Explorer 

## Example kilos mask with max 3 integers:

    <input type="text" class="form-control" id="masked-1">

    $('#masked-1').maskWeight({
     integerDigits: 3,
     decimalDigits: 3,
     decimalMark: ',',
     //initVal default: generated
     //roundingZeros default: true
    });

## Options
Plugin offers few options to customize mask:
   
    {
     integerDigits: 3,
     decimalDigits: 3,
     decimalMark: '.',
     initVal: '000,000',
     roundingZeros: true
    }

**integerDigits** default 3. Describes number of integers before decimal mark

**decimalDigits** default 3. Describes number of decimal places after decimal mark. **Can be 0, then mask works only with integers!**

**decimalMark** default '.' (dot). Sets custom decimal mark

**initVal** Sets custom initial value. Default generated based on **integerDigits ** and **decimalMark** and **decimalDigits** and **roundingZeros**. For example if we set those options we get **0,000**:

    {
     integerDigits: 2,
     decimalDigits: 3,
     decimalMark: ',',
     roundingZeros: true
    }

For those options we get **000,00**:
   
    {
     integerDigits: 3,
     decimalDigits: 2,
     decimalMark: ',',
     roundingZeros: false
    }

**roundingZeros** default true. Determine if zeros in integer digits (before decimal mark) should be rounded. For example if real typed value will be 001,555 then after rounding input value will be 1,555. If setted to false zeros wil be shown in input.

## License & Author
Copyright (c) 2016 Dawid Senko, Licensed under the MIT license (http://opensource.org/licenses/mit-license.php)