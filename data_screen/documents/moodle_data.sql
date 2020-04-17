---------------------------------------------------------------------------
-- 表的结构
---------------------------------------------------------------------------

--
-- 表的结构 `mdl_block_access_statistics`
--
CREATE TABLE `mdl_block_access_statistics` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `pv` bigint(10) DEFAULT NULL,
  `uv` bigint(10) DEFAULT NULL,
  `ip` bigint(10) DEFAULT NULL,
  `access_num` bigint(10) DEFAULT '0',
  `date` bigint(10) DEFAULT NULL,
  `updated_time` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Statistic access' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_access_top`
--
CREATE TABLE `mdl_block_access_top` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `counts` bigint(10) NOT NULL DEFAULT '0',
  `date` bigint(10) NOT NULL,
  `updated_time` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Top view' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_college_avg`
--
CREATE TABLE `mdl_block_college_avg` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `course_num_avg` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `student_num_avg` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `teacher_num_avg` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `resource_num_avg` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_time` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to show college avg' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_college_total`
--
CREATE TABLE `mdl_block_college_total` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `college_id` bigint(10) DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `teacher_id` bigint(10) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `effective_num` bigint(10) DEFAULT NULL,
  `course_num` bigint(10) NOT NULL,
  `student_num` bigint(10) NOT NULL,
  `teacher_num` bigint(10) DEFAULT NULL,
  `resource_num` bigint(10) NOT NULL,
  `type` bigint(10) NOT NULL,
  `updated_time` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to show college' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_count_visit`
--
CREATE TABLE `mdl_block_count_visit` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `path` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `section_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource_type` bigint(10) NOT NULL,
  `resource_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `visit_num` bigint(10) NOT NULL,
  `updated_time` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to show count visit' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_data_total`
--
CREATE TABLE `mdl_block_data_total` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `effective_num` bigint(10) NOT NULL,
  `course_num` bigint(10) NOT NULL,
  `teacher_num` bigint(10) NOT NULL,
  `student_num` bigint(10) NOT NULL,
  `percourse_num` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `updated_time` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to show data screen' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_user_enrol_course`
--
CREATE TABLE `mdl_block_user_enrol_course` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `coursename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `shortname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `categoryid` bigint(10) NOT NULL,
  `categoryname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `start_time` bigint(10) NOT NULL,
  `end_time` bigint(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to show mes' ROW_FORMAT=COMPRESSED;

--
-- 表的结构 `mdl_block_course_total`
--
CREATE TABLE `mdl_block_course_total` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `courseid` bigint(10) NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shortname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` bigint(10) DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` bigint(10) DEFAULT NULL,
  `end_time` bigint(10) DEFAULT NULL,
  `students` bigint(10) DEFAULT '0',
  `spendtime` bigint(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='course data' ROW_FORMAT=COMPRESSED;



---------------------------------------------------------------------------
-- 收集数据的存储过程
---------------------------------------------------------------------------

-- 查询已开课程（去除无用课程）
-- 执行顺序：1
DROP PROCEDURE IF EXISTS p_block_user_enrol_course;
DELIMITER ;;
CREATE PROCEDURE p_block_user_enrol_course()
BEGIN
	TRUNCATE mdl_block_user_enrol_course;
	INSERT INTO mdl_block_user_enrol_course(courseid,coursename,shortname,categoryid,categoryname,path,start_time,end_time)
		SELECT 
			DISTINCT c.id,
			c.fullname,
			c.shortname,
			cc.id,
			cc.name,
			cc.path,
			c.startdate,
			c.enddate
		FROM mdl_course c 
		JOIN mdl_context ct ON c.id=ct.instanceid 
		JOIN mdl_role_assignments ra ON ra.contextid=ct.id
		JOIN mdl_course_categories cc ON c.category=cc.id
		WHERE 1<(SELECT COUNT(*) FROM mdl_course_modules cm WHERE cm.course = c.id)
		AND c.fullname NOT LIKE '%测试%'
		AND c.shortname NOT LIKE '%测试%'
		AND 3 IN (SELECT roleid FROM mdl_role_assignments ra WHERE ra.contextid=ct.id);
END ;;
DELIMITER ;

-- 课程数据统计
-- 执行顺序：2
DROP PROCEDURE IF EXISTS p_course_total;
DELIMITER ;;
CREATE PROCEDURE p_course_total()
BEGIN
	TRUNCATE mdl_block_course_total;
	INSERT INTO mdl_block_course_total(courseid,fullname,shortname,category,path,start_time,end_time,students)
		SELECT 
			c.id courseid,
			c.fullname,
			c.shortname,
			c.category,
			cc.path,
			c.startdate start_time,
			c.enddate end_time,
			(SELECT COUNT(userid) FROM mdl_role_assignments r JOIN mdl_context con ON con.id=r.contextid WHERE con.instanceid=c.id AND r.roleid=5)students
		FROM mdl_course c
		JOIN mdl_course_categories cc ON cc.id=c.category;
		
	CALL p_course_activetime;
END ;;
DELIMITER ;

-- 开设课程访问时长（秒）
-- 访问时长记录在 mdl_block_course_total
-- 执行顺序：3 
DROP PROCEDURE IF EXISTS p_course_activetime;
DELIMITER ;;
CREATE PROCEDURE p_course_activetime()
BEGIN
	DECLARE this_time INT;
	DECLARE this_course INT;
	DECLARE this_user INT;
	DECLARE last_time INT DEFAULT 0;
	DECLARE last_course INT DEFAULT 0;
	DECLARE last_user INT DEFAULT 0;
	DECLARE done INT DEFAULT FALSE;
	
	DECLARE log_cur CURSOR FOR (SELECT courseid, timecreated,userid FROM mdl_logstore_standard_log WHERE userid>0 AND courseid>0 ORDER BY courseid,userid,FROM_UNIXTIME(timecreated));
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	UPDATE mdl_block_course_total SET spendtime=0;
	
	OPEN log_cur;
	cur_loop:LOOP
		FETCH log_cur INTO this_course,this_time,this_user;
		
		IF done THEN
			LEAVE cur_loop;
		END IF;
		
		IF (this_time-last_time)<(30*60) AND this_user=last_user AND this_course=last_course THEN
			UPDATE mdl_block_course_total SET spendtime=spendtime+(this_time-last_time) WHERE courseid=this_course;
		END IF;
		
		SET last_time=this_time;
		SET last_course=this_course;
		SET last_user=this_user;
	END LOOP;
	CLOSE log_cur;
END ;;
DELIMITER ;

-- 统计各学院开设课程情况（开课数，教师数，学生数）
-- 执行顺序：4
DROP PROCEDURE IF EXISTS p_college_total;
DELIMITER ;;
CREATE PROCEDURE p_college_total()
BEGIN
	TRUNCATE mdl_block_college_total;
	INSERT INTO mdl_block_college_total(college_id,path,name,effective_num,course_num,student_num,teacher_num,resource_num,type,updated_time)
	SELECT 
		cc.id college_id,
		cc.path,
		cc.name,
		(SELECT COUNT(*) 
			FROM `mdl_block_user_enrol_course` c 
			WHERE cc.id=c.categoryid 
		) effective_num,
		(SELECT COUNT(*) FROM mdl_course c JOIN mdl_course_categories cate ON cate.id=c.category WHERE c.id <> 1 AND cate.path LIKE CONCAT('/',cc.id,'%')) course_num,
		(SELECT COUNT(distinct ra.userid)
			FROM mdl_role_assignments ra
			JOIN mdl_context ct ON ct.id=ra.contextid
			JOIN mdl_course c ON c.id=ct.instanceid
			JOIN mdl_course_categories cate ON cate.id=c.category
			WHERE cate.path LIKE CONCAT('/',cc.id,'%')
			-- 排除无用课程
			-- AND c.id IN (SELECT courseid FROM mdl_block_user_enrol_course)
			AND ra.roleid=5
		) student_num,
		(SELECT COUNT(DISTINCT ra.userid)
			FROM mdl_role_assignments ra
			JOIN mdl_context ct ON ct.id=ra.contextid
			JOIN mdl_course c ON c.id=ct.instanceid
			JOIN mdl_course_categories cate ON cate.id=c.category
			WHERE cate.path LIKE CONCAT('/',cc.id,'%')
			-- 排除无用课程
			-- AND c.id IN (SELECT courseid FROM mdl_block_user_enrol_course)
			AND ra.roleid=3
		) teacher_num,
		(SELECT 
			COUNT(DISTINCT cm.id)
			FROM mdl_course_modules cm
			JOIN mdl_course c ON c.id=cm.course
			JOIN mdl_course_categories cate ON cate.id=c.category
			WHERE cate.path LIKE CONCAT('/',cc.id,'%')
		) resource_num,
		1 type,
		UNIX_TIMESTAMP(NOW()) updated_time
	FROM `mdl_course_categories` cc
	WHERE cc.parent=0;
	CALL p_teacher_total;
END ;;
DELIMITER ;
-- 统计教师开课情况(课程数)
-- 执行顺序：5
-- 已在 p_college_total 中调用
-- 必须先执行 p_college_total 后才能调用
DROP PROCEDURE IF EXISTS p_teacher_total;
DELIMITER ;;
CREATE PROCEDURE p_teacher_total()
BEGIN
    DECLARE teacher INT DEFAULT 0;
    DECLARE done INT DEFAULT FALSE;
    DECLARE teacher_cur CURSOR FOR (SELECT DISTINCT u.id FROM mdl_user u JOIN mdl_role_assignments ra ON ra.userid=u.id AND ra.roleid=3); 
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE; 

    OPEN teacher_cur;
    cur_loop:LOOP
        FETCH teacher_cur INTO teacher;

        IF done THEN
            LEAVE cur_loop;
        END IF;

        INSERT INTO mdl_block_college_total(teacher_id,name,course_num,student_num,resource_num,type,updated_time)
			SELECT 
				ra.userid teacher_id,
				u.firstname name,
				COUNT(DISTINCT c.id) course_num,
				(SELECT COUNT(DISTINCT ra.userid) FROM mdl_role_assignments ra WHERE ra.contextid IN (SELECT r.contextid FROM mdl_role_assignments r JOIN mdl_user user ON user.id=r.userid WHERE r.roleid=3 AND user.id=u.id) AND ra.roleid=5) student_num,
				(SELECT COUNT(cm.id) FROM mdl_course_modules cm WHERE cm.course IN (SELECT t.instanceid FROM mdl_role_assignments r JOIN mdl_context t ON t.id=r.contextid WHERE r.roleid=3 AND r.userid=u.id)) resource_num,
				0 type,
				UNIX_TIMESTAMP(NOW()) updated_time
			FROM mdl_user u
			JOIN mdl_role_assignments ra ON ra.userid=u.id
			JOIN mdl_context ct ON ct.id=ra.contextid
			JOIN mdl_course c ON c.id=ct.instanceid
			WHERE u.id=2
			AND ra.roleid=3;
	END LOOP;
	CLOSE teacher_cur;
END ;;
DELIMITER ;

-- 平台概况：平台开课数、平台总课程数、平台教师数、平台学生数、人均选课数
-- 人均选课计算：已被选的课程数/已选课的学生
-- 执行顺序：6
DROP PROCEDURE IF EXISTS p_data_total;
DELIMITER ;;
CREATE PROCEDURE p_data_total()
BEGIN
	DECLARE studentcounts INT DEFAULT 0;
	DECLARE bechosen INT DEFAULT 0;
	SELECT counts INTO studentcounts FROM (SELECT COUNT(DISTINCT userid) counts FROM mdl_role_assignments WHERE roleid=5) stucount;
	SELECT counts INTO bechosen FROM (SELECT COUNT(DISTINCT c.id) counts FROM mdl_course c JOIN mdl_context ct ON ct.instanceid=c.id JOIN mdl_role_assignments ra ON ra.contextid=ct.id WHERE ra.roleid=5) bc;
	TRUNCATE mdl_block_data_total;
	INSERT INTO mdl_block_data_total(effective_num,course_num,teacher_num,student_num,percourse_num,updated_time)
		SELECT 
			SUM(effective_num),
			SUM(course_num),
			SUM(teacher_num),
			studentcounts,
			ROUND(bechosen/studentcounts, 2),
			UNIX_TIMESTAMP(NOW())
		 FROM mdl_block_college_total
		 WHERE type=1;
END ;;
DELIMITER ;

-- 浏览次数pv、独立访客uv、IP、访问数量
-- 执行顺序：7
DROP PROCEDURE p_access_statistics;
DELIMITER ;;
CREATE PROCEDURE p_access_statistics()
BEGIN
	DECLARE this_time INT;
	DECLARE this_user INT;
	DECLARE last_time INT DEFAULT 0;
	DECLARE last_user INT DEFAULT 0;
	DECLARE done INT DEFAULT FALSE;
	
	DECLARE log_cur CURSOR FOR (SELECT timecreated,userid FROM mdl_logstore_standard_log ORDER BY TO_DAYS(FROM_UNIXTIME(timecreated)),userid,FROM_UNIXTIME(timecreated));
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	TRUNCATE mdl_block_access_statistics;
	INSERT INTO mdl_block_access_statistics(pv,uv,ip,date,updated_time)
		SELECT 
			COUNT(action="viewed" OR NULL) pv,
			COUNT(DISTINCT CASE WHEN userid>=0 THEN userid END) uv,
			COUNT(DISTINCT ip) ip,
			timecreated date,
			UNIX_TIMESTAMP(NOW()) updated_time
		FROM mdl_logstore_standard_log
		GROUP BY TO_DAYS(FROM_UNIXTIME(timecreated));
		
	OPEN log_cur;
	cur_loop:LOOP
		FETCH log_cur INTO this_time,this_user;
		IF done THEN
			LEAVE cur_loop;
		END IF;
		
		IF this_user!=last_user OR (this_time-last_time)>(30*60) THEN
			UPDATE mdl_block_access_statistics SET access_num=access_num+1 WHERE TO_DAYS(FROM_UNIXTIME(date))=TO_DAYS(FROM_UNIXTIME(this_time));
		END IF;
		
		SET last_time=this_time;
		SET last_user=this_user;
	END LOOP;
	CLOSE log_cur;
	
	CALL p_access_top;
END ;;
DELIMITER ;
-- 历史访问最高记录
-- 执行顺序：8
-- 已在 p_access_statistics 中调用
DROP PROCEDURE IF EXISTS p_access_top;
DELIMITER ;;
CREATE PROCEDURE p_access_top()
BEGIN
	TRUNCATE mdl_block_access_top;
	
	INSERT INTO mdl_block_access_top(type,counts,date,updated_time) SELECT 'pv',pv,date,UNIX_TIMESTAMP(NOW()) FROM mdl_block_access_statistics WHERE pv=(SELECT MAX(pv) FROM mdl_block_access_statistics) LIMIT 1;
	INSERT INTO mdl_block_access_top(type,counts,date,updated_time) SELECT 'uv',uv,date,UNIX_TIMESTAMP(NOW()) FROM mdl_block_access_statistics WHERE uv=(SELECT MAX(uv) FROM mdl_block_access_statistics) LIMIT 1;
	INSERT INTO mdl_block_access_top(type,counts,date,updated_time) SELECT 'ip',ip,date,UNIX_TIMESTAMP(NOW()) FROM mdl_block_access_statistics WHERE ip=(SELECT MAX(ip) FROM mdl_block_access_statistics) LIMIT 1;
	INSERT INTO mdl_block_access_top(type,counts,date,updated_time) SELECT 'access_num',access_num,date,UNIX_TIMESTAMP(NOW()) FROM mdl_block_access_statistics WHERE access_num=(SELECT MAX(access_num) FROM mdl_block_access_statistics) LIMIT 1;
END ;;
DELIMITER ;

	


---------------------------------------------------------------------------
-- 程序中用到的SQL
---------------------------------------------------------------------------

-- 查询无用课程（不在已开课程中的课程为无用课程）
SELECT id,fullname,shortname FROM `mdl_course` WHERE id NOT IN (SELECT DISTINCT id FROM mdl_block_user_enrol_course)

-- 统计各类课程开设情况
SELECT 
	c.id,
	c.fullname,
	cc.id cid,
	cc.name cname,
	COUNT(c.id)counts
FROM mdl_course c 
JOIN mdl_tag_instance ti ON c.id=ti.itemid
JOIN mdl_course_categories cc ON cc.id=c.category
WHERE ti.itemtype='course' 
AND ti.tagid=7 
OR ti.tagid=13  
GROUP BY id;

-- 返回该学院的全部课程及选课人数
SELECT
	ec.courseid,
	ec.coursename,
	(SELECT COUNT(DISTINCT ra.userid) FROM mdl_role_assignments ra JOIN mdl_context ct ON ct.id=ra.contextid WHERE ct.instanceid=ec.courseid AND ra.roleid=5)counts
FROM mdl_block_user_enrol_course ec 
WHERE ec.path like '/3%';

-- 获取学院老师列表详情(老师id，姓名，课程数，学生人数，资源数)
SELECT 
	bc.teacher_id,
	u.lastname,
	bc.course_num,
	bc.student_num,
	bc.resource_num 
FROM mdl_block_college_total bc 
JOIN mdl_user u ON u.id=bc.teacher_id 
WHERE college_id=2;

-- 获取该教师的课程列表：coursename
SELECT 
	DISTINCT c.fullname  
FROM mdl_course c 
JOIN mdl_context ct ON ct.instanceid=c.id 
JOIN mdl_role_assignments ra ON ra.contextid=ct.id 
WHERE ra.userid=1 
AND ra.roleid=3;


-- 获取教师列表
SELECT DISTINCT u.id,u.firstname FROM mdl_user u JOIN mdl_role_assignments ra ON ra.userid=u.id WHERE ra.roleid=3;

-- 获取教师平均课程访问时长
SELECT 
	SUM(c.spendtime)spendtime
FROM mdl_block_course_total c 
JOIN mdl_context ct ON ct.instanceid=c.courseid 
JOIN mdl_role_assignments ra ON ra.contextid=ct.id 
WHERE ra.userid=2 
AND ra.roleid=3;

-- 课程绩效（单个课程）
-- 有无课程简介（summary）、（未完成）
-- 课程封面（mdl_files,component=course,filearea=overviewfiles,contextid)、http://192.168.138.142/pluginfile.php/58/course/overviewfiles/ces.jpg 58为contextid
-- 资源数（视频+PPT+PDF+链接）、
-- 讨论区数、作业数、测验数。
-- 各主题下各类活动建设情况（资源、讨论区、作业、测验及其他，其中资源包括h5p、文件、文件夹、网页、网页地址）(未完成)
SELECT
	c.id courseid,
	c.fullname,
	-- c.summary,
	(SELECT COUNT(f.id) FROM mdl_files f JOIN mdl_context ct ON ct.id=f.contextid WHERE SUBSTRING(f.mimetype,1,5)='video' AND ct.instanceid=c.id) video,
    (SELECT COUNT(f.id) FROM mdl_files f JOIN mdl_context ct ON ct.id=f.contextid WHERE f.mimetype='application/vnd.ms-powerpoint' AND ct.instanceid=c.id) ppt,
    (SELECT COUNT(f.id) FROM mdl_files f JOIN mdl_context ct ON ct.id=f.contextid WHERE f.mimetype='application/pdf' AND ct.instanceid=c.id) pdf,
	(SELECT COUNT(id) FROM mdl_url WHERE course=c.id) url,
	(SELECT COUNT(id) FROM mdl_quiz WHERE course=c.id) quiz,
	(SELECT COUNT(id) FROM mdl_assign WHERE course=c.id) assign,
	(SELECT COUNT(id) FROM mdl_forum_discussions WHERE course=c.id)discussions
FROM mdl_course c 
WHERE c.id=2

SELECT module,course,section FROM mdl_course_modules WHERE course=2 ORDER BY section;

-- 资源访问日志
-- contextinstanceid 为 mdl_course_modules id
SELECT 
	log.userid,
	log.courseid,
	log.timecreated,
	cm.module,
	cm.instance
FROM mdl_logstore_standard_log log
JOIN mdl_course_modules cm ON cm.id=log.contextinstanceid
WHERE component LIKE "%mod_%"
ORDER BY log.userid,cm.module,log.timecreated;



-- 查询 mdl_block_count_visit 中的记录
SELECT * FROM mdl_block_count_visit WHERE course_id=2 AND resource_type=1 AND resource_id=1;

-- 学生访问统计
-- A.资源：统计各学生的完成情况/点击数、访问时长、成绩（h5p）
-- B.作业：统计各学生的提交情况、得分
-- C.测验：统计各学生的提交情况、得分、结果分析
-- D.讨论：统计各学生的发帖数、回帖数、老师/助教的发帖数、回帖数、讨论得分
-- 各学生的课程完成率、活动访问数、课程访问次数、课程访问时长

-- 学生对资源的点击数，访问时长
SELECT ra.id,ra.userid,c.id courseid FROM mdl_role_assignments ra JOIN mdl_context ct ON ct.id=ra.contextid JOIN mdl_course c ON c.id=ct.instanceid WHERE ra.roleid=5;
SELECT
	cm.course,
	cm.module,
	cm.instance,
	log.timecreated
FROM mdl_logstore_standard_log log
JOIN mdl_course_modules cm ON cm.id=log.contextinstanceid
WHERE component LIKE '%mod_%'
AND log.userid=2
ORDER BY cm.course,log.timecreated;


-- for 循环计算访问时长
-- 学生课程访问时长
SELECT 
	courseid, 
	timecreated,
	userid 
FROM mdl_logstore_standard_log 
WHERE userid>0 
AND courseid>0 
ORDER BY userid,courseid,timecreated


INSERT INTO mdl_block_count_visit(course_id,resource_type,resource_id,num,section_name) 
SELECT 
	cm.course course_id,
	cm.module resource_type,
	cm.instance resource_id,
	cs.section num,
	cs.name section_name
FROM mdl_course_modules cm 
JOIN mdl_course_sections cs ON cm.section=cs.id
WHERE cm.module IN (1,3,4,5,6,7,8,9,10,12,13,15,16,17,20);

-- 开设二轮的课程
SELECT name FROM mdl_block WHERE name='associated_course';
SELECT DISTINCT courseid FROM mdl_block_course_type;

-- 课程学生提交数
-- 应提交数=选课人数、提交率=提交人数/应提交数
SELECT COUNT(sub.id) FROM mdl_assign_submission sub JOIN mdl_assign a ON a.id=sub.assignment WHERE a.course=2 AND sub.userid IN 
(
	SELECT ra.userid FROM mdl_role_assignments ra JOIN mdl_context ct ON ct.id=ra.contextid WHERE ct.instanceid=a.course AND ra.roleid=5
)
-- 作业批改数
SELECT COUNT(ag.id) FROM mdl_assign_grades ag JOIN mdl_assign a ON a.id=ag.assignment WHERE a.course=2 AND ag.grader=2;
-- 平均成绩
SELECT AVG(ag.grade) FROM mdl_assign_grades ag JOIN mdl_assign a ON a.id=ag.assignment WHERE a.course=2 AND ag.grader=2;

-- 课程讨论区发帖数
SELECT
	f.id forumid,
	f.name,
	f.course,
	(SELECT COUNT(fd.id) FROM mdl_forum_discussions fd JOIN mdl_context ct ON ct.instanceid=fd.course JOIN mdl_role_assignments ra ON ra.contextid=ct.id WHERE fd.forum=f.id AND fd.userid=ra.userid AND ra.roleid=3) teacher_posts,
	(SELECT COUNT(fp.id) FROM mdl_forum_posts fp JOIN mdl_forum_discussions fd ON fd.id=fp.discussion JOIN mdl_context ct ON ct.instanceid=fd.course JOIN mdl_role_assignments ra ON ra.contextid=ct.id WHERE ra.userid=fd.userid AND ra.roleid=3 AND fp.parent<>0 AND fd.forum=f.id) reply_teacher,
	(SELECT COUNT(fd.id) FROM mdl_forum_discussions fd JOIN mdl_context ct ON ct.instanceid=fd.course JOIN mdl_role_assignments ra ON ra.contextid=ct.id WHERE fd.forum=f.id AND fd.userid=ra.userid AND ra.roleid=5) student_posts,
	(SELECT COUNT(fp.id) FROM mdl_forum_posts fp JOIN mdl_forum_discussions fd ON fd.id=fp.discussion JOIN mdl_context ct ON ct.instanceid=fd.course JOIN mdl_role_assignments ra ON ra.contextid=ct.id WHERE ra.userid=fd.userid AND ra.roleid=5 AND fp.parent<>0 AND fd.forum=f.id) reply_student
FROM mdl_forum f;

-- 讨论区时长
SELECT 
	cm.instance forum,
	log.timecreated
FROM mdl_logstore_standard_log log
JOIN mdl_course_modules cm ON cm.id=log.contextinstanceid
WHERE cm.module=9
AND eventname LIKE '%mod_forum%' 
ORDER BY log.contextinstanceid,log.timecreated;

-- 课程完成学生人数（排除已被撤销选课的学生）
SELECT 
	COUNT(cc.userid) 
FROM mdl_course_completions cc
WHERE cc.course=8
AND cc.timecompleted<>0
AND cc.userid IN 
(
	SELECT 
		ra.userid 
	FROM mdl_role_assignments ra 
	JOIN mdl_context ct ON ct.id=ra.contextid 
	WHERE ra.roleid=5 
	AND ct.instanceid=cc.course
);


---------------------------------------------------------------------------
-- 未整理的SQL
---------------------------------------------------------------------------


-- 课程资源访问统计
SELECT 
	courseid,
	c.fullname,
	COUNT(target='course' OR NULL) access_course,
	COUNT(objecttable='assign' OR NULL) access_assign,
	COUNT(objecttable='quiz' OR NULL) quiz,
	COUNT(objecttable='url' OR NULL) url,
	COUNT(objecttable='book' OR NULL) book
FROM `mdl_logstore_stANDard_log` l 
JOIN mdl_course c ON c.id=l.courseid
GROUP BY courseid

-- 课程封面
SELECT 
	f.id,
	f.filename,
	f.component,
	f.filearea,
	f.contextid
FROM mdl_files f
join mdl_context ct on f.contextid=ct.id
join mdl_course c on c.id=ct.instanceid
where c.id=4
AND f.component='course'
AND f.filearea='overviewfiles'
AND f.filename<>'.';

-- 课程列表:包括课程名称、学生数、开设时间（startdate字段mdl_course)
SELECT 
	id courseid,
	fullname, 
	(SELECT COUNT(DISTINCT userid) FROM `v_user_enrol_course` WHERE courseid=id AND roleid=5) studentcounts, 
	startdate 
FROM mdl_course

-- 统计登录次数 mdl_logstore_stANDard_log
SELECT u.id,u.firstname,u.lastname,(SELECT count(*) FROM mdl_logstore_stANDard_log l where action='loggedin' AND u.id=l.userid) counts FROM `mdl_user` as u

-- 学院资源统计（课程、测验、资源、文件夹）
SELECT 
	cc.id, cc.name,
	(SELECT count(*) FROM mdl_course where category=cc.id) counts,
	(SELECT count(*) FROM mdl_quiz where course in (SELECT id FROM mdl_course where category=cc.id)) quiz,
	(SELECT count(*) FROM mdl_resource where course in (SELECT id FROM mdl_course where category=cc.id)) resource,
	(SELECT count(*) FROM mdl_folder where course in (SELECT id FROM mdl_course where category=cc.id)) folder
FROM mdl_course_categories cc;


-- 平台资源访问统计：测验、作业、文件夹、文件
SELECT 
	COUNT(objecttable='quiz' OR NULL) quiz,
	COUNT(objecttable='assign' OR NULL) assign,
	COUNT(objecttable='folder' OR NULL) folder，
	COUNT(objecttable='resource' OR NULL) resource
FROM mdl_logstore_ogstore_stANDard_log WHERE objecttable<>'';


-- 课程全称、课程简称、课程开始时间、课程结束时间、课程教师、课程选课人数
DROP VIEW IF EXISTS v_user_enrol_course;
CREATE VIEW v_user_enrol_course AS
SELECT 
	c.fullname,
	c.shortname,
	c.startdate,
	c.enddate,
	(SELECT GROUP_CONCAT(u.firstname) FROM mdl_role_assignments r JOIN mdl_context con ON con.id=r.contextid JOIN mdl_user u ON u.id=r.userid WHERE con.instanceid=c.id AND r.roleid=3)teachers,
	(SELECT COUNT(userid) FROM mdl_role_assignments r JOIN mdl_context con ON con.id=r.contextid WHERE con.instanceid=c.id AND r.roleid=5)students
FROM mdl_course c;

-- 课程学生数
SELECT 
	c.id courseid,
	c.fullname,
	c.shortname,
	c.category,
	cc.path,
	c.startdate start_time,
	c.enddate end_time,
	(SELECT COUNT(userid) FROM mdl_role_assignments r JOIN mdl_context con ON con.id=r.contextid WHERE con.instanceid=c.id AND r.roleid=5)students
FROM mdl_course c
JOIN mdl_course_categories cc ON cc.id=c.category;

-- 学院名称 查出上级学院
SELECT 
	(CASE WHEN cc.parent=0 THEN cc.name ELSE (SELECT name FROM mdl_course_categories WHERE id=SUBSTRING_INDEX(SUBSTRING_INDEX(cc.path,'/',2),'/',-1)) END)name,
	c.fullname,
	c.shortname,
	c.startdate,
	c.enddate,
	(SELECT GROUP_CONCAT(username) FROM mdl_role_assignments r JOIN mdl_user u ON u.id = r.userid JOIN mdl_context con ON con.id = r.contextid WHERE con.instanceid = c.id AND r.roleid = 3 ) teachers,
	(SELECT COUNT(userid) FROM mdl_role_assignments r JOIN mdl_context con ON con.id = r.contextid  WHERE con.instanceid = c.id AND r.roleid = 5 ) students 
FROM mdl_course c 
JOIN mdl_course_categories cc ON cc.id=c.category

-- 无用课程
SELECT * FROM 
(
	SELECT 
		c.id,
		c.fullname,
		c.shortname,
		c.category,
		(SELECT COUNT(ra.userid) FROM mdl_role_assignments ra JOIN mdl_context ct ON ct.id=ra.contextid WHERE ct.instanceid=c.id AND ra.roleid=3) teacher_counts,
		(SELECT COUNT(ra.userid) FROM mdl_role_assignments ra JOIN mdl_context ct ON ct.id=ra.contextid WHERE ct.instanceid=c.id AND ra.roleid=5) student_counts,
		(SELECT COUNT(id) FROM mdl_course_modules cm WHERE cm.course=c.id) resource_counts
	FROM mdl_course c WHERE c.id <> 1
) course
WHERE teacher_counts<1 OR resource_counts<2 OR fullname LIKE '%测试%' OR shortname LIKE '%测试%';











