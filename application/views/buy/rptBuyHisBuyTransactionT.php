<?php
if ($login_status) {
	require_once ("application/views/public/login_in.php");
} else {
	require_once ("application/views/public/login_out.php");
}
?>
<script type="text/javascript">
			var timeLeft;
			var syncInterval;
			$(function() {
				syncInterval = setInterval(syncTimeLeft, 300000);
				syncTimeLeft();
			});
		
			function resetCountdown(timeLeft) {
		
				window.setInterval(function() {
					var day = 0, hour = 0, minute = 0, second = 0;//时间默认值		
					if (timeLeft > 0) {
						day = Math.floor(timeLeft / (60 * 60 * 24));
						hour = Math.floor(timeLeft / (60 * 60)) - (day * 24);
						minute = Math.floor(timeLeft / 60) - (day * 24 * 60)
								- (hour * 60);
						second = Math.floor(timeLeft) - (day * 24 * 60 * 60)
								- (hour * 60 * 60) - (minute * 60);
					}
					if (hour <= 9)
						hour = '0' + hour;
					if (minute <= 9)
						minute = '0' + minute;
					if (second <= 9)
						second = '0' + second;
					document.getElementById("hour_show").innerHTML = '' + hour + ':'
							+ minute + ':' + second + '';
					timeLeft--;
					if (timeLeft == 0) {
						clearInterval(syncInterval);
						location.reload();
					}
				}, 1000);
		
			}
		
			function syncTimeLeft() {
				$
						.getJSON(
								'/timeSet/getStatues2?date=' + new Date(),
								function(data) {
									if (data[0]['statues']=='Y') {
										$('#statusName').text("离交易结束还剩");
									} else {
										$('#statusName').text("离交易开始还剩");
									}
									
									timeLeft = data[0]['second'];
									if (timeLeft <= 0) {
										document.getElementById("hour_show").innerHTML = '00:00:00';
										clearInterval(syncInterval);
										//return;
									} else {
										resetCountdown(timeLeft);
									}
								});
			}
		</script>

<link type="text/css" rel="stylesheet"
	href="/static/components/sdmenu/css/sdmenu.css" />
<script type="text/javascript">
			var myMenu;
			window.onload = function() {
				myMenu = new SDMenu("my_menu");
				myMenu.init();
			};
			function SDMenu(id) {
				if (!document.getElementById || !document.getElementsByTagName)
					return false;
				this.menu = document.getElementById(id);
				this.submenus = this.menu.getElementsByTagName("div");
				this.remember = true;
				this.speed = 3;
				this.markCurrent = true;
				this.oneSmOnly = false;
			}
			SDMenu.prototype.init = function() {
				var mainInstance = this;
				for (var i = 0; i < this.submenus.length; i++)
					this.submenus[i].getElementsByTagName("span")[0].onclick = function() {
						mainInstance.toggleMenu(this.parentNode);
					};
				if (this.markCurrent) {
					var links = this.menu.getElementsByTagName("a");
					for (var i = 0; i < links.length; i++)
						if (document.location.href.indexOf(links[i].href) >= 0) {
							links[i].className = "current";
							break;
						}
				}
				if (this.remember) {
					var regex = new RegExp("sdmenu_" + encodeURIComponent(this.menu.id)
							+ "=([01]+)");
					var match = regex.exec(document.cookie);
					if (match) {
						var states = match[1].split("");
						for (var i = 0; i < states.length; i++)
							this.submenus[i].className = (states[i] == 0 ? "collapsed"
									: "");
					}
				}
			};
			SDMenu.prototype.toggleMenu = function(submenu) {
				if (submenu.className == "collapsed")
					this.expandMenu(submenu);
				else
					this.collapseMenu(submenu);
			};
			SDMenu.prototype.expandMenu = function(submenu) {
				var fullHeight = submenu.getElementsByTagName("span")[0].offsetHeight;
				var links = submenu.getElementsByTagName("a");
				for (var i = 0; i < links.length; i++)
					fullHeight += links[i].offsetHeight;
				var moveBy = Math.round(this.speed * links.length);
		
				var mainInstance = this;
				var intId = setInterval(function() {
					var curHeight = submenu.offsetHeight;
					var newHeight = curHeight + moveBy;
					if (newHeight < fullHeight)
						submenu.style.height = newHeight + "px";
					else {
						clearInterval(intId);
						submenu.style.height = "";
						submenu.className = "";
						mainInstance.memorize();
					}
				}, 30);
				this.collapseOthers(submenu);
			};
			SDMenu.prototype.collapseMenu = function(submenu) {
				var minHeight = submenu.getElementsByTagName("span")[0].offsetHeight;
				var moveBy = Math.round(this.speed
						* submenu.getElementsByTagName("a").length);
				var mainInstance = this;
				var intId = setInterval(function() {
					var curHeight = submenu.offsetHeight;
					var newHeight = curHeight - moveBy;
					if (newHeight > minHeight)
						submenu.style.height = newHeight + "px";
					else {
						clearInterval(intId);
						submenu.style.height = "";
						submenu.className = "collapsed";
						mainInstance.memorize();
					}
				}, 30);
			};
			SDMenu.prototype.collapseOthers = function(submenu) {
				if (this.oneSmOnly) {
					for (var i = 0; i < this.submenus.length; i++)
						if (this.submenus[i] != submenu
								&& this.submenus[i].className != "collapsed")
							this.collapseMenu(this.submenus[i]);
				}
			};
			SDMenu.prototype.expandAll = function() {
				var oldOneSmOnly = this.oneSmOnly;
				this.oneSmOnly = false;
				for (var i = 0; i < this.submenus.length; i++)
					if (this.submenus[i].className == "collapsed")
						this.expandMenu(this.submenus[i]);
				this.oneSmOnly = oldOneSmOnly;
			};
			SDMenu.prototype.collapseAll = function() {
				for (var i = 0; i < this.submenus.length; i++)
					if (this.submenus[i].className != "collapsed")
						this.collapseMenu(this.submenus[i]);
			};
			SDMenu.prototype.memorize = function() {
				if (this.remember) {
					var states = new Array();
					for (var i = 0; i < this.submenus.length; i++)
						states.push(this.submenus[i].className == "collapsed" ? 0 : 1);
					var d = new Date();
					d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
					document.cookie = "sdmenu_" + encodeURIComponent(this.menu.id)
							+ "=" + states.join("") + "; expires=" + d.toGMTString()
							+ "; path=/";
				}
			};
		</script>


<link type="text/css" rel="stylesheet"
	href="/static/components/extremetable/Styles/extremecomponents.css" />
<link href="/static/components/jqueryui/Styles/datepicker.css"
	type="text/css" rel="stylesheet" />
<script
	src="/static/components/jqueryui/Scripts/jquery-ui-1.10.3.custom.min.js"
	type="text/javascript"></script>
<script type="text/javascript"
	src="/static/components/jquery/validation/jquery.validate.min.js"
	type="text/javascript"></script>
<script type="text/javascript"
	src="/static/components/jquery/validation/messages_zh.js"></script>
<script type="text/javascript"
	src="/static/components/jquery/validation/jquery-validate.bootstrap-tooltip.js" /></script>
<script type="text/javascript">
	$(function() {
		$(".datepicker").datepicker(
				{
					showMonthAfterYear : true,
					monthNamesShort : [ '一月', '二月', '三月', '四月', '五月', '六月',
							'七月', '八月', '九月', '十月', '十一月', '十二月' ],
					dayNamesMin : [ '日', '一', '二', '三', '四', '五', '六' ],
					dateFormat : "yy-mm-dd",
					changeYear : true,
					changeMonth : true
				});
	});

	$(document).ready(function() {
		$("#queryform").validate({
			rules : {
				BuyCode : {
					maxlength : 8
				},
				PrdCode : {
					maxlength : 40
				},
				GrdCode : {
					maxlength : 40
				},
				BeginDate : {
					required : true
				},
				EndDate : {
					required : true
				}
			}
		});
	})
</script>

		<ol class="breadcrumb">
			您现在的位置：
			<li><a href="/welcome">首页</a></li>
			<li><a href="/buyer/myflower">我的花拍</a></li>
			<li class="active">交易汇总</li>
		</ol>


		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-info">
					<div class="panel-heading" style="text-align: center;">
						<a href="/buyer/index">继续交易</a>
					</div>
				</div>
				<div class="panel panel-success">
					<div class="panel-heading">我的花拍</div>
					<div class="panel-body"
						style="padding: 0px 0px 36px 0px; text-align: center">

						<div id="my_menu" class="sdmenu">
							<div class="collapsed">
								<span>个人中心</span> <a href="/buyer/changePassword">密码重置</a> <a
									href="/buyer/ibuyer">信息修改</a>
							</div>
							<div>
								<span>交易中心</span> <a href="/buyer/netPay">网银充值</a> <a
									href="/buyer/drawBack">退款申请</a> <a href="/buyer/buyerLimit">限额查询</a>
								<a href="/buyer/transaction">交易明细</a> <a
									href="/buyer/rptBuyBuyTransactionT">交易汇总</a>
							</div>
							<div class="collapsed">
								<span>结算中心</span> <a href="/buyer/buyRestBill">其它应收应付</a> <a
									href="/buyer/totalBalance">结算查询</a>
							</div>
							<div class="collapsed">
								<span>历史查询</span> <a href="/buyer/rptBuyHisBuyTransactionD">交易明细</a>
								<a href="/buyer/rptBuyHisBuyTransactionT">交易汇总</a> <a
									href="/buyer/rptBuyHisLogdeListD">投诉明细</a> <a
									href="/buyer/rptBuyHisBuyPreBankFundD">充值明细</a> <a
									href="/buyer/rptBuyHisBalanceList">结算明细</a>
							</div>
							<div class="collapsed">
								<span>交易规则</span> <a
									href="/buyer/webInBuyerRuleForBuyerContent/我要购买">我要购买</a> <a
									href="/buyer/webInBuyerRuleForBuyerContent/物流方式">物流方式</a> <a
									href="/buyer/webInBuyerRuleForBuyerContent/支付及结算">支付及结算</a> <a
									href="/buyer/webInBuyerRuleForBuyerContent/质量及标准">质量及标准</a> <a
									href="/buyer/webInBuyerRuleForBuyerContent/我要投诉">我要投诉</a>
							</div>
							<div class="collapsed">
								<span>常见问题</span> <a
									href="/buyer/webInBuyerProblemForBuyerContent/购买问题">购买问题</a> <a
									href="/buyer/webInBuyerProblemForBuyerContent/物流问题">物流问题</a> <a
									href="/buyer/webInBuyerProblemForBuyerContent/支付问题">支付问题</a> <a
									href="/buyer/webInBuyerProblemForBuyerContent/系统操作问题">系统操作问题</a>
								<a href="/buyer/webInBuyerProblemForBuyerContent/其它问题">其它问题</a>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="col-md-9" style="padding-left: 0px;">
				<div class="panel panel-success">
					<div class="panel-heading">交易汇总</div>

					<!--交易汇总-->
					<form class="form-inline" role="form" id="queryform"
						name="queryform" method="post"
						action="/buyer/rptBuyHisBuyTransactionT/query">
						<input type="text" class="form-control" id="PrdCode"
							name="PrdCode" value="" placeholder="品种"> <select name="GrdCode"
							id="GrdCode" class="form-control">
							<option value="">等级</option>
							<option value='A'>A</option>
							<option value='B'>B</option>
							<option value='C'>C</option>
						</select> <input type="text" class="form-control datepicker"
							id="BeginDate" name="BeginDate" value="" size="10" required
							placeholder="起始日期" /> <input type="text"
							class="form-control datepicker" id="EndDate" name="EndDate"
							value="" size="10" required placeholder="终止日期" />
						<button type="submit" class="btn btn-default">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</form>
					<br>


					<div class="table-responsive">




















						<form id="ec" action="/buyer/rptBuyHisBuyTransactionT/query"
							method="post">
							<div>
								<input type="hidden" name="ec_i" value="ec" /> <input
									type="hidden" name="ec_eti" /> <input type="hidden"
									name="ec_ev" /> <input type="hidden" name="ec_efn" /> <input
									type="hidden" name="ec_crd" value="100" /> <input type="hidden"
									name="ec_p" value="1" /> <input type="hidden"
									name="ec_s_TraBreedName" /> <input type="hidden"
									name="ec_s_TraPrdCode" /> <input type="hidden"
									name="ec_s_TraPrdName" /> <input type="hidden"
									name="ec_s_TraGrdCode" /> <input type="hidden"
									name="ec_s_TraCount" /> <input type="hidden"
									name="ec_s_Quantity" /> <input type="hidden"
									name="ec_s_TraMaxPrice" /> <input type="hidden"
									name="ec_s_TraMinPrice" /> <input type="hidden"
									name="ec_s_TraAvgPrice" /> <input type="hidden"
									name="ec_s_TraAmount" /> <input type="hidden"
									name="ec_s_TraSupComm" /> <input type="hidden"
									name="ec_s_TraGetAmount" />
							</div>
							<div class="eXtremeTable">
								<table id="ec_table" border="0" cellspacing="0" cellpadding="0"
									class="tableRegion" width="100%">
									<thead>
										<tr style="padding: 0px;">
											<td colspan="13">
												<table border="0" cellpadding="0" cellspacing="0"
													width="100%">
													<tr>
														<td class="statusBar">没有找到记录.</td>
														<td class="compactToolbar" align="right">
															<table border="0" cellpadding="1" cellspacing="2">
																<tr>
																	<td><img
																		src="/static/components/extremetable/images/table/firstPageDisabled.gif"
																		style="border: 0" alt="第一页" /></td>
																	<td><img
																		src="/static/components/extremetable/images/table/prevPageDisabled.gif"
																		style="border: 0" alt="上一页" /></td>
																	<td><img
																		src="/static/components/extremetable/images/table/nextPageDisabled.gif"
																		style="border: 0" alt="下一页" /></td>
																	<td><img
																		src="/static/components/extremetable/images/table/lastPageDisabled.gif"
																		style="border: 0" alt="最后页" /></td>
																	<td><img
																		src="/static/components/extremetable/images/table/separator.gif"
																		style="border: 0" alt="Separator" /></td>
																	<td><select name="ec_rd"
																		onchange="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_crd.value=this.options[this.selectedIndex].value;document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()">
																			<option value="100" selected="selected">100</option>
																			<option value="50">50</option>
																			<option value="100" selected="selected">100</option>
																	</select></td>
																	<td><img
																		src="/static/components/extremetable/images/table/separator.gif"
																		style="border: 0" alt="Separator" /></td>
																	<td><a
																		href="javascript:document.forms.ec.ec_eti.value='ec';document.forms.ec.ec_ev.value='xls';document.forms.ec.ec_efn.value='buyHisBuyTransactionT.xls';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"><img
																			src="/static/components/extremetable/images/table/xls.gif"
																			style="border: 0" title="导出 Excel" alt="导出XLS" /></a></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>

										<tr>
											<td class="tableHeader">序号</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraBreedName.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 品类">品类</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraPrdCode.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 品种代码">品种代码</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraPrdName.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 品种名称">品种名称</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraGrdCode.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 等级">等级</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraCount.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 笔数">笔数</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_Quantity.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 成交数量">成交数量</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraMaxPrice.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 最高价">最高价</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraMinPrice.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 最低价">最低价</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraAvgPrice.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 均价">均价</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraAmount.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 成交金额">成交金额</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraSupComm.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 佣金">佣金</td>
											<td class="tableHeader"
												onmouseover="this.className='tableHeaderSort';this.style.cursor='pointer'"
												onmouseout="this.className='tableHeader';this.style.cursor='default'"
												onclick="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_s_TraGetAmount.value='asc';document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"
												title="排序 划账金额">划账金额</td>
										</tr>
									</thead>
									<tbody class="tableBody">
										<tr class="calcRow">
											<td class="calcTitle">合计</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td>&#160;</td>
											<td class="calcResult">0.00</td>
											<td class="calcResult">0.00</td>
											<td class="calcResult">0.00</td>
										</tr>
									</tbody>
									<tr style="padding: 0px;">
										<td colspan="13">
											<table border="0" cellpadding="0" cellspacing="0"
												width="100%">
												<tr>
													<td class="statusBar">没有找到记录.</td>
													<td class="compactToolbar" align="right">
														<table border="0" cellpadding="1" cellspacing="2">
															<tr>
																<td><img
																	src="/static/components/extremetable/images/table/firstPageDisabled.gif"
																	style="border: 0" alt="第一页" /></td>
																<td><img
																	src="/static/components/extremetable/images/table/prevPageDisabled.gif"
																	style="border: 0" alt="上一页" /></td>
																<td><img
																	src="/static/components/extremetable/images/table/nextPageDisabled.gif"
																	style="border: 0" alt="下一页" /></td>
																<td><img
																	src="/static/components/extremetable/images/table/lastPageDisabled.gif"
																	style="border: 0" alt="最后页" /></td>
																<td><img
																	src="/static/components/extremetable/images/table/separator.gif"
																	style="border: 0" alt="Separator" /></td>
																<td><select name="ec_rd"
																	onchange="javascript:document.forms.ec.ec_eti.value='';document.forms.ec.ec_crd.value=this.options[this.selectedIndex].value;document.forms.ec.ec_p.value='1';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()">
																		<option value="100" selected="selected">100</option>
																		<option value="50">50</option>
																		<option value="100" selected="selected">100</option>
																</select></td>
																<td><img
																	src="/static/components/extremetable/images/table/separator.gif"
																	style="border: 0" alt="Separator" /></td>
																<td><a
																	href="javascript:document.forms.ec.ec_eti.value='ec';document.forms.ec.ec_ev.value='xls';document.forms.ec.ec_efn.value='buyHisBuyTransactionT.xls';document.forms.ec.setAttribute('action','/buyer/rptBuyHisBuyTransactionT/query');document.forms.ec.setAttribute('method','post');document.forms.ec.submit()"><img
																		src="/static/components/extremetable/images/table/xls.gif"
																		style="border: 0" title="导出 Excel" alt="导出XLS" /></a></td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>

								</table>
							</div>
						</form>

					</div>

				</div>
			</div>
		</div>


	</div>
		


<?php
require_once ("application/views/public/footer.php");
?>