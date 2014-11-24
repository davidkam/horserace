var horses;
var win;
var scratched;
var pot = 0;
var conseq = 0;
var horseImg = '<img src="/images/horse.png" class="horse">';
var msg = '';

$( document ).ready(function() {
  hrInit();
  $(".roll" ).click(function() {
    processRoll(this);
  });
});

function hrInit() {
  horses = init.horses;
  win = init.win;
  scratched = init.scratched;
  for(var horse in horses) {
    horsepos = horses[horse];
    cell = 'cell-' + horse + '-' + (horsepos + 1);
    $('.' + cell).html(horseImg);
  }
}

function processRoll(obj) {
  var classname = $(obj).attr('class');
  var temp = classname.split(" ");
  var buff = temp[1].split('-');
  var rolled = parseInt(buff[1]);
  var doubles = buff[2];
  var statuscode = roll(rolled);
  if(doubles == 'd') {
    conseq++;
    if(conseq == 2) {
      if(msg != '') {
        msg += '<br /><br />';
      }
      msg += 'Take a shot';
    }
    if(conseq > 2) {
      if(msg != '') {
        msg += '<br /><br />';
      }
      msg += 'Order somebody to take a shot';
    }
    if(msg != '') {
      msg += '<br /><br />';
    }
    msg += 'Roll again';
  } else {
    conseq = 0;
  }

  if(msg != '') {
    showMsg(msg);
    msg = '';
  }
}

function roll(rolled) {
  var statuscode = '';
  // check to see scratched is filled
  if(scratched.length < 4) {
    // check for already rolled
    if(scratched.indexOf(rolled) == -1) {
      $('.row-' + rolled).addClass('scratched');
      $('.cell-' + rolled + '-1').html('');
      scratched.push(rolled);
      scnt = scratched.length;
      $('.scratch-' + scnt).html(rolled);
      payUpScratch(rolled);
      statuscode = 'scratch';
    } else {
      payUp(rolled);
      statuscode = 'pay';
    }
  //check to see if should pay
  } else if(scratched.indexOf(rolled) != -1) {
    payUp(rolled);
    statuscode = 'pay';   
  //legit, advance
  } else {
    statuscode = advance(rolled);
  }
  return statuscode;
}

function advance(rolled) {
  var statuscode = '';
  horsepos = horses[rolled];
  horsepos++;
  cell = 'cell-' + rolled + '-' + (horsepos + 1);
  if(horsepos < win[rolled]) {
    horses[rolled] = horsepos;
    $('.row-' + rolled +' td.cellelement').html('');
    $('.' + cell).html(horseImg);
  } else {
    //cell = 'cell-' + rolled + '-' + (horsepos);
    $('.row-' + rolled +' td.cellelement').html('');
    $('.row-' + rolled + ' td.horsenum').html(horseImg);
    msg = 'Horse ' + rolled + ' wins!!<br /><br />Total pot: ' + pot;
    $('.roll').unbind('click');
    $('.status').html('');
    statuscode = 'win';
  }
  return statuscode;
}

function payUp(rolled) {
  var pos = scratched.indexOf(rolled);
  amt = pos + 1;
  pot += amt;
  msg = 'Pay ' + amt;
}

function payUpScratch(rolled) {
  var pos = scratched.indexOf(rolled);
  amt = pos + 1;
  pot += (4 * amt);
  msg = 'Everybody pay ' + amt + ' per ' + rolled;
}

function showMsg(flash) {
  $.colorbox({html:'<div class="msg"><table><tr><td>' + flash + '</td></tr></table></div>'});
}
