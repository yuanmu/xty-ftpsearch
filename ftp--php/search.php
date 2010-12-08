﻿///////////////////////////////
//该文件通过get方法接受界面传过来的“关键词”，关键词变量是keyword
////////////////////////////////
///////////////////////////////
////该文件需加入html标签的地方文件中有标识
///////////////////////////////
<?
require("splitword.php");         //调用分词类文件
require("common/function.php");   //调用自定义函数文件
if($_GET){                        //应用GET方法获取搜索框的关键字
$keyword=$_GET[keyword];
}

if($submit2!=""){						    //在查询结果中查找
	$h_keyword=$hide_keyword;			//获取原始的值	
	$keynew=$keyword;
	$h_keyword.=$keyword;				//获取老值+新值
 	$keyword=$h_keyword;				//将最新的值赋给keyword
}

$yuan=trim($keyword);         //获取用户输入的关键词，并去除左右两边空格
$tt= $yuan;                   //将去除左右空格的关键词赋给变量$tt

$str=gl($tt);                 //对关键词过滤标点符号

$sp = new SplitWord();        //创建分词对象

//显示时间部分
$time_start = getmicrotime(); //开始计时，这个是备选项
$sp->SplitRMM($str);          //调用分词方法，对关键词进行分词操作
$tt=$sp->SplitRMM($str);      //将分词后的结果赋给变量$tt

?>
////////////

///////////
<?php
	$str=array(" ","");				//定义一个数组
	$cc=str_replace($str,"",$tt);	//去掉字符串中的空格
	if(substr($cc,0,2)=="、"){
		$cc= substr($cc,2);			//去掉前面的“、”符号
	}
	if(substr($cc,-2,2)=="、"){
		$cc= substr($cc,0,-2);		//去掉后面的“、”符号
	}
	
	if(substr($cc,0,2)=="、" && substr($cc,-2,2)=="、"){
		$a= substr($cc,2);			//去掉前面的“、”符号
		$cc= substr($a,0,-2);		//去掉后面的“、”符号
	}
		$newstr = explode("、",$cc);			//应用explode()函数将字符串转换成数组
	
	
	require_once("./common/db_mysql.class.php");  //调用数据访问类文件
	$DB = new DB_MySQL;				//创建对象
	if(count($newstr)==1){					//如果数组的元素个数为1个，则按单个条件进行查询
			 $sql = "select * from catalog_table,file_table where cat like '%".$newstr[0]."%' or file like '%".$newstr[0]."%'";   //order by id desc "
		}
		else{
			for($i=0;$i<count($newstr);$i++){      //循环输出目录表中与之匹配的各个关键词
				$sql0.=" cat like '%".trim($newstr[$i])."%'"." or";	
			}
			for($j=0;$j<count($newstr);$j++){      //循环输出文件表中与之匹配的各个关键词
				$sql1.=" file like '%".trim($newstr[$j])."%'"." or";	
			}
			$sql1=substr($sql1,0,-2);				//去掉最后一个“or”		
			$sql="select * from catalog_table,file_table where".$sql0.$sql1;       //形成数据库查询语言
			}
  
  $DB->query($sql);      //发送SQL语句到MySQL服务器
	$res = $DB->get_rows_array();  //将结果储存在数组中
	$rows_count=count($res);         //计算结果总数
	$time_end = getmicrotime();				//结束计时
	$t0 = $time_end - $time_start;			//搜索计时
	?>
	//////////////////
	
	////////////////////利用循坏语句获取输出数据表中各个字段的值
	<?php                 
	for($i=0;$i<$rows_count;$i++){
		$id=$res[$i]['id'];         //获取ID号
		$title=$res[$i]['title'];      //获取标题
		$content=$res[$i]['content'];    //获取内容
	?>

////////////
		
///////////在网页上显示您查找的关键词
//////////需加html标签
		<?php
		  echo $keyword;
		  ?>
///////////
		  
 //////////汇总匹配的条数，在网页上显示
 //////////需加html标签
		  <?php
		  echo $row_count_sum;
		  ?>
///////////
		  
 //////////在网页上显示计算搜索的时间
 //////////需加html标签
		  <?php
		    echo "用时：$t0秒";
		    ?>
///////////////
		    
//////////////以分页的形式输出符合条件的信息给用户
		    <?php
	if($_GET){
		//得到要提取的页码
		$page_num = $_GET['page_num']? $_GET['page_num']: 1;
	}
	else{
		//首次进入时,页码为1
		$page_num = 1;
	}
	
	//得到总记录数
	$DB->query($sql);
	$row_count_sum = $DB->get_rows();
	$row_count_sum;
	//每页记录数,可以使用默认值或者直接指定值
	$row_per_page = 9;
	//总页数
	$page_count = ceil($row_count_sum/$row_per_page);
	//判断是否为第一页或者最后一页
	$is_first = (1 == $page_num) ? 1 : 0;
	$is_last = ($page_num == $page_count) ? 1 : 0;
	//查询起始行位置
	$start_row = ($page_num-1) * $row_per_page;
	//为SQL语句添加limit子句
	$sql .= " limit $start_row,$row_per_page";
	//执行查询
	$DB->query($sql);
	$res = $DB->get_rows_array();
	//结果集行数
	$rows_count=count($res);
	?>
	/////////
	
	/////////对标题中所有符合查询关键词后的词语进行描红和超链接，这里应用循坏语句实现将对搜素搜索结果进行输出
	/////////需加html标签
	<?php
		 	for($n=0;$n<count($newstr);$n++){   //应用FOR循环语句对分词后的词语进行描红
				 $title= str_ireplace($newstr[$n],"<font color='#FF0000'>".$newstr[$n]."</font>",$title);
			}
		   echo chinesesubstr($title,0,80);    //输出标题的前80个字节
		   if(strlen($title)>80){ echo "...";} //如果长度超过80字节，则输出"..."
		  ?>
/////////////////////////////////////////////////////////////////////////////////////////

//////////////////输出“第一页”、“上一页”、“下一页”、“最后一页”文字的超链接
/////////////////需加html标签
<?php
			if(!$is_first){
			?>
      <a href="./search.php?page_num=1&keyword=<?php echo $keyword;?>">第一页</a> 
      <a href="./search.php?page_num=<?php echo ($page_num-1); ?>&keyword=<?php echo $keyword;?>">上一页</a>
            <?php
			}
			else{
			?>
            第一页&nbsp;&nbsp;上一页
            <?php
			}
			if(!$is_last){
			?>
            <a href="./search.php?page_num=<?php echo ($page_num+1); ?>&keyword=<?php echo $keyword;?>">下一页</a> 
            <a href="./search.php?page_num=<?php echo $page_count; ?>&keyword=<?php echo $keyword;?>">最后一页</a>
            <?php
			}
			else
			{
			?>
            下一页&nbsp;&nbsp;最后一页
            <?php
			}
			?>
	  