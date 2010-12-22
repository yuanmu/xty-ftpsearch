﻿<!--///////////////////////////////
//该文件通过get方法接受界面传过来的“关键词”，关键词变量是keyword
////////////////////////////////
///////////////////////////////
////该文件需加入html标签的地方文件中有标识
///////////////////////////////-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
<?
require("splitword.php");         //调用分词类文件
require("common/function.php");   //调用自定义函数文件
if($_POST){                        //应用GET方法获取搜索框的关键字
   $keyword=$_POST[keyword];
    echo $keyword;
 	//$keyword="你好";
}
//$keyword="dianying";
$yuan=trim($keyword);         //获取用户输入的关键词，并去除左右两边空格
$tt= $yuan;                   //将去除左右空格的关键词赋给变量$tt

$str1=gl($tt);                 //对关键词过滤标点符号
//$str=gl1($str1);               //过滤标点符号

$sp = new SplitWord();        //创建分词对象

//显示时间部分
$time_start = getmicrotime(); //开始计时，这个是备选项
$sp->SplitRMM($str);          //调用分词方法，对关键词进行分词操作
$tt=$sp->SplitRMM($str);      //将分词后的结果赋给变量$tt

?>
<!--////////////

///////////-->
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
	//	echo "htttp";
	//	for($u=0;$u<count($newstr);$u++)
	//	{
	//		echo $newstr[$u];
	//	}
  require_once("common/db_mysql.class.php");  //调用数据访问类文件
	$DB = new DB_MySQL;				//创建对象
	if(count($newstr)==1){					//如果数组的元素个数为1个，则按单个条件进行查询
			 $sql = "select * from cat,files where cat like '%".$newstr[0]."%' or file like '%".$newstr[0]."%'";   //order by id desc "
}
		else{
			for($i=0;$i<count($newstr);$i++){      //循环输出目录表中与之匹配的各个关键词
				$sql0.=" cat like '%".trim($newstr[$i])."%'"." or";	
			}
			for($j=0;$j<count($newstr);$j++){      //循环输出文件表中与之匹配的各个关键词
				$sql1.=" file like '%".trim($newstr[$j])."%'"." or";	
			} 
			$sql1=substr($sql1,0,-2);				//去掉最后一个“or”		
			$sql="select * from cat,files where".$sql0.$sql1;       //形成数据库查询语言
			}
  
  $DB->query($sql);      //发送SQL语句到MySQL服务器
	$res = $DB->get_rows_array();  //将结果储存在数组中
	$rows_counts=count($res);         //计算结果总数
	$ii= mysql_num_fields($DB->query($sql));
	echo $ii;
	$time_end = getmicrotime();				//结束计时
	$t0 = $time_end - $time_start;			//搜索计时
	//echo $rows_counts;
	//echo $t0;

	?>
<!--	///////////在网页上显示您查找的关键词
//////////需加html标签-->
		<?php
		  echo $keyword;
		  ?>
<!--///////////
		  
 //////////汇总匹配的条数，在网页上显示
 //////////需加html标签-->
		  <?php
		  echo $rows_counts;
		  ?>
<!--///////////
		  
 //////////在网页上显示计算搜索的时间
 //////////需加html标签-->
		  <?php
		    echo "用时：".$t0."秒";
		    ?>
		   <!--///////////////
		    
//////////////以分页的形式输出符合条件的信息给用户-->
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
	//$row_count_sum;
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
	$sql.= " limit $start_row,$row_per_page";
	//执行查询
	$DB->query($sql);
	$res = $DB->get_rows_array();
	$rows_count=count($res);
	echo $rows_count;
	for($i=0;$i<$rows_count;$i++){
      $result= array();
      echo $res[$i]['file'];
    //  $true=0;
    //  for($j=0;$j<count($newstr);$j++)
    //  {
      	 //$t=substr_count($res[$i]['file'],$newstr[j]);
    //  	if(substr_count($res[$i]['file'],$newstr[j]))
    //  	   { 
   //   	   	$true=1;
    //  	    }
  //    }

      if($res[$i]['file'])     //输出文件表中所对应的文件名、父目录、站点地址得出完整地址
	    {
			 $file=$res[$i]['file'];
			 $postfix=$res[$i]['postfix'];
			 $pid=$res[$i]['pid'];
			 $ipid=$res[$i]['ipid'];
			 $id1="\\";
			 while($pid!=0)
			 {
			 	$DB->query("select * from cat where id=$pid");
			 	$result1= $DB->get_rows_array();
			 	$pid=$result1[0]['pid'];
			 	$result1[0]['cat'].=$id1;
			 	$id1="\\".$result1[0]['cat'];
			 }
			 $DB->query("select * from ftpinfo where id=$ipid");
			 $result2= $DB->get_rows_array();
			 echo $result2[0]['port'];
			 $result2[0]['port'].=$id1;
			 $id1=":".$result2[0]['port'];
			 $result2[0]['site'].=$id1;
			 $id1=$result2[0]['site'];
			 $file.=".".$postfix;
			 $id1.=$file;
			 $result[i]=$id1;
			// $true=0;
	    } 
	    else                        ////输出目录表中所对应的目录名、父目录、站点地址得出完整地址
		  {
			 $cat=$res[$i]['cat'];
			 $pid=$res[$i]['pid'];
			 $ipid=$res[$i]['ipid'];
			 $id2="\\";
			 while($pid!=0)
			 {
			 	$DB->query("select * from cat where id=$pid");
			 	$result1= $DB->get_rows_array();
			 	$pid=$result1[0]['pid'];
			 	$result1[0]['cat'].=$id2;
			 	$id2="\\".$result1[0]['cat'];
			 }
			 $DB->query("select * from ftpinfo where id=$ipid");
			 $result2= $DB->get_rows_array();
			 echo $result2[0]['port'];
			 $result2[0]['port'].=$id2;
			 $id2=":".$result2[0]['port'];
			 $result2[0]['site'].=$id2;
			 $id2=$result2[0]['site'];
			 $id2.=$cat;
			 $result[i]=$id2;
		  } 
	  }
		
	?>
	<!--/////////
	
	/////////对标题中所有符合查询关键词后的词语进行描红和超链接，这里应用循坏语句实现将对搜素搜索结果进行输出
	/////////需加html标签-->
	<?php
		 	for($i=0;$i<$rows_count;$i++){
		 	for($n=0;$n<count($newstr);$n++){   //应用FOR循环语句对分词后的词语进行描红
				 $result[i]= str_ireplace($newstr[$n],"<font color='#FF0000'>".$newstr[$n]."</font>",$result[i]);
			}
		 ?>	
		 <a href="<?php echo $result[i];?>"><?php echo $result[i];?></a>
			<?php
			  }	
			  ?>
			  <!--/////////////////////////////////////////////////////////////////////////////////////////

//////////////////输出“第一页”、“上一页”、“下一页”、“最后一页”文字的超链接
/////////////////需加html标签-->
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
	</body>
</html>