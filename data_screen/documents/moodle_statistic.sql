-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 10.248.53.31:3306
-- Generation Time: 2019-05-05 10:43:23
-- 服务器版本： 5.7.12
-- PHP Version: 7.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moodle_statistic`
--

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_activetimeResource` (`var_courseid` INT)  BEGIN
	select courseid, type, section, sectionname, moduleid, resourcename, sum(spendtime) spendtime, sum(logcounts) logcounts from t_activetime_individual where (type='resource' or type='folder') and courseid=var_courseid group by courseid, type, moduleid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_assignmentState` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
	select cm.id moduleid, a.name, case when sub.submitintime is null then 2 else sub.submitintime end submitstate from moodle.mdl_assign a left join (select * from v_assign_submission where courseid=var_courseid and userid=var_userid) sub on a.id=sub.assignid join moodle.mdl_course_modules cm on a.id=cm.instance where a.course=var_courseid and cm.course=var_courseid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_assignNotSubmissionList` (`var_courseid` BIGINT(10), `var_assignid` BIGINT(10))  BEGIN
	select uec.courseid, uec.userid, uec.firstname, uec.lastname
		from t_user_enrol_course uec where courseid=var_courseid and userid not in (select userid from v_assign_submission where assignid=var_assignid);
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_courseModuleList` (IN `courseid` INTEGER)  BEGIN
select @sequence:=@sequence+1 sequence, m.name moduletype, cs.section, cs.name sectionname, 
CASE
WHEN m.name='resource' THEN (select name from moodle.mdl_resource where id=cm.instance)
WHEN m.name='forum' THEN (select name from moodle.mdl_forum where id=cm.instance)
WHEN m.name='quiz' THEN (select name from moodle.mdl_quiz where id=cm.instance)
WHEN m.name='page' THEN (select name from moodle.mdl_page where id=cm.instance)
WHEN m.name='assign' THEN (select name from moodle.mdl_assign where id=cm.instance)
WHEN m.name='folder' THEN (select name from moodle.mdl_folder where id=cm.instance)
WHEN m.name='url' THEN (select name from moodle.mdl_url where id=cm.instance)
WHEN m.name='chat' THEN (select name from moodle.mdl_chat where id=cm.instance)
WHEN m.name='choice' THEN (select name from moodle.mdl_choice where id=cm.instance)
WHEN m.name='hvp' THEN (select name from moodle.mdl_hvp where id=cm.instance)
WHEN m.name='questionnaire' THEN (select name from moodle.mdl_questionnaire where id=cm.instance)
WHEN m.name='lti' THEN (select name from moodle.mdl_lti where id=cm.instance)
WHEN m.name='workshop' THEN (select name from moodle.mdl_workshop where id=cm.instance)
WHEN m.name='wiki' THEN (select name from moodle.mdl_wiki where id=cm.instance)
WHEN m.name='glossary' THEN (select name from moodle.mdl_glossary where id=cm.instance)
WHEN m.name='lesson' THEN (select name from moodle.mdl_lesson where id=cm.instance)
WHEN m.name='data' THEN (select name from moodle.mdl_data where id=cm.instance)
WHEN m.name='book' THEN (select name from moodle.mdl_book where id=cm.instance)
WHEN m.name='attendance' THEN (select name from moodle.mdl_attendance where id=cm.instance)
WHEN m.name='survey' THEN (select name from moodle.mdl_survey where id=cm.instance)
WHEN m.name='scorm' THEN (select name from moodle.mdl_scorm where id=cm.instance)
WHEN m.name='certificate' THEN (select name from moodle.mdl_certificate where id=cm.instance)
END name
from moodle.mdl_course_modules cm 
join moodle.mdl_modules m on cm.module=m.id 
join moodle.mdl_course_sections cs on cm.section=cs.id,
(select @sequence:=0) init
where cm.course=courseid and cs.course=courseid
and (m.name='resource' or m.name='forum' or m.name='forum_discussions' or m.name='forum_discussion_subs' or m.name='forum_subscriptions' or m.name='quiz' or m.name='quiz_attempts' or m.name='page' or m.name='assign' or m.name='assignsubmission_file' or m.name='assignsubmission_onlinetext' or m.name='assign_submission' or m.name='folder' or m.name='url' or m.name='chat' or m.name='chat_messages' or m.name='choice' or m.name='choice_answers' or m.name='hvp' or m.name='questionnaire' or m.name='lti' or m.name='workshop' or m.name='wiki' or m.name='glossary' or m.name='glossary_entries' or m.name='lesson' or m.name='lesson_pages' or m.name='data' or m.name='data_fields' or m.name='data_records' or m.name='book' or m.name='book_chapters' or m.name='attendance' or m.name='attendance_sessions' or m.name='attendance_statuses' or m.name='survey' or m.name='scorm' or m.name='scorm_scoes' or m.name='scorm_scoes_track' or m.name='certificate')
order by cm.section, cm.added;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_lowLearningState` (`var_courseid` BIGINT(10), `var_threshold` FLOAT)  BEGIN
	select 
	userid, firstname, lastname,
	case when spendtimepoints<avgspendtimepoints*var_threshold then 1 else 0 end spendtimestate,
	case when quizcompleted=1 then case when quizpoints<avgquizpoints*var_threshold then 1 else 0 end else 2 end quizstate,
	case when assignmentpoints<avgassignmentpoints*var_threshold then 1 else 0 end assignmentstate
	from t_total_point t join moodle.mdl_user u on t.userid=u.id where courseid=var_courseid and totalpoints<avgtotalpoints*var_threshold;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_moduleAccessIndividual` (IN `courseid` INTEGER)  BEGIN
select a.userid, b.sequence, a.counts from 
	t_activetime_individual_logcounts a join
	(select @sequence:=@sequence+1 sequence, m.name moduletype, cs.section, cs.name sectionname, cm.instance, 
CASE
WHEN m.name='resource' THEN (select name from moodle.mdl_resource where id=cm.instance)
WHEN m.name='forum' THEN (select name from moodle.mdl_forum where id=cm.instance)
WHEN m.name='quiz' THEN (select name from moodle.mdl_quiz where id=cm.instance)
WHEN m.name='page' THEN (select name from moodle.mdl_page where id=cm.instance)
WHEN m.name='assign' THEN (select name from moodle.mdl_assign where id=cm.instance)
WHEN m.name='folder' THEN (select name from moodle.mdl_folder where id=cm.instance)
WHEN m.name='url' THEN (select name from moodle.mdl_url where id=cm.instance)
WHEN m.name='chat' THEN (select name from moodle.mdl_chat where id=cm.instance)
WHEN m.name='choice' THEN (select name from moodle.mdl_choice where id=cm.instance)
WHEN m.name='hvp' THEN (select name from moodle.mdl_hvp where id=cm.instance)
WHEN m.name='questionnaire' THEN (select name from moodle.mdl_questionnaire where id=cm.instance)
WHEN m.name='lti' THEN (select name from moodle.mdl_lti where id=cm.instance)
WHEN m.name='workshop' THEN (select name from moodle.mdl_workshop where id=cm.instance)
WHEN m.name='wiki' THEN (select name from moodle.mdl_wiki where id=cm.instance)
WHEN m.name='glossary' THEN (select name from moodle.mdl_glossary where id=cm.instance)
WHEN m.name='lesson' THEN (select name from moodle.mdl_lesson where id=cm.instance)
WHEN m.name='data' THEN (select name from moodle.mdl_data where id=cm.instance)
WHEN m.name='book' THEN (select name from moodle.mdl_book where id=cm.instance)
WHEN m.name='attendance' THEN (select name from moodle.mdl_attendance where id=cm.instance)
WHEN m.name='survey' THEN (select name from moodle.mdl_survey where id=cm.instance)
WHEN m.name='scorm' THEN (select name from moodle.mdl_scorm where id=cm.instance)
WHEN m.name='certificate' THEN (select name from moodle.mdl_certificate where id=cm.instance)
END name
from moodle.mdl_course_modules cm 
join moodle.mdl_modules m on cm.module=m.id 
join moodle.mdl_course_sections cs on cm.section=cs.id,
(select @sequence:=0) init
where cm.course=courseid and cs.course=courseid
order by cm.section, cm.added) b on a.type=b.moduletype and a.resourceid=b.instance and a.courseid=courseid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_notCompletedQuiz` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
	select cm.id moduleid, q.name from moodle.mdl_quiz q join moodle.mdl_course_modules cm on q.id=cm.instance where q.id not in (select quizid from v_quiz_grade where courseid=var_courseid and userid=var_userid) and q.course=var_courseid and cm.course=var_courseid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_personalAssignSubmission` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
select cm.id moduleid, cs.section sectionid, cs.name sectionname, a.name, sub.timemodified, a.duedate, a.duedate-unix_timestamp(sysdate()) timeleft, if(sub.status is null,'notsubmitted',sub.status) status, if(ag.grade is null, -1, ag.grade) grade
	from moodle.mdl_assign a 
	left join moodle.mdl_assign_submission sub on a.id=sub.assignment and sub.userid=var_userid
	left join moodle.mdl_assign_grades ag on a.id=ag.assignment and sub.assignment=ag.assignment and sub.attemptnumber=ag.attemptnumber and ag.userid=var_userid
	left join (select id, instance, section, count(*) from moodle.mdl_course_modules where course=var_courseid group by instance) cm on a.id=cm.instance
	left join moodle.mdl_course_sections cs on cm.section=cs.id
	where a.course=var_courseid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_personalQuiz` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
	select cm.id moduleid, cs.section sectionid, cs.name sectionname, q.name, round(q.timelimit/60,1) timelimit, q.attempts, if(qa.attemptcounts is null, 0, qa.attemptcounts) attemptcounts, q.timeclose, q.timeclose-unix_timestamp(sysdate()) timeleft, if(qg.grade is null, 0.0, qg.grade) grade, aqg.avggrade, round(q.grade,1) sumgrades, if(r.rank is null, 0, r.rank) rank , aqg.counts, if(r.rank is null, 0, round(r.rank/aqg.counts*100,1)) percent
		from moodle.mdl_quiz q 
		join moodle.mdl_course_modules cm on q.id=cm.instance and cm.course=var_courseid
		left join moodle.mdl_course_sections cs on cm.section=cs.id
		left join (select quiz, count(*) attemptcounts from moodle.mdl_quiz_attempts where userid=var_userid group by quiz) qa on q.id=qa.quiz
		left join moodle.mdl_quiz_grades qg on qg.quiz=q.id and qg.userid=var_userid
		left join (select qg.quiz, round(avg(qg.grade),1) avggrade, count(*) counts from moodle.mdl_quiz_grades qg join moodle.mdl_quiz q on qg.quiz=q.id where q.course=var_courseid group by qg.quiz) aqg on aqg.quiz=q.id
		left join (select if(@pquiz=quiz,@hrank:=@hrank+1,@hrank:=1) hrank, if(@pgrade=grade,@rank:=@rank,@rank:=@hrank) rank, qg.*, (@pquiz:=quiz), (@pgrade:=grade) from (select qg.* from moodle.mdl_quiz_grades qg join moodle.mdl_quiz q on qg.quiz=q.id where q.course=var_courseid order by qg.quiz, qg.grade desc) qg, (select @pquiz:=null,@rank:=0,@hrank:=0) init) r on q.id=r.quiz and r.userid=var_userid
		where q.course=var_courseid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_personalQuizAttempts` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
	select qa.id attemptid, cm.id moduleid, qa.state, qa.sumgrades grade, qa.timefinish from moodle.mdl_quiz q join moodle.mdl_quiz_attempts qa on qa.quiz=q.id join moodle.mdl_course_modules cm on q.id=cm.instance and cm.course=var_courseid where q.course=var_courseid and qa.userid=var_userid order by quiz, attemptid desc;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_personalResourceLackTime` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
select t.type, t.section, t.sectionname, t.moduleid, t.resourcename, round(sum(t.spendtime)/sec.counts,1)-ind.spendtime lacktime from t_activetime_individual t join v_student_enrol_course_counts sec on t.courseid=sec.courseid and t.courseid=var_courseid and sec.courseid=var_courseid and t.type!='quiz' join (select * from t_activetime_individual where userid=var_userid and courseid=var_courseid) ind on t.moduleid=ind.moduleid group by t.courseid, t.type, t.moduleid having lacktime>0 order by round(sum(t.spendtime)/sec.counts,1)-ind.spendtime desc;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_personalResourceNotAccess` (`var_courseid` BIGINT(10), `var_userid` BIGINT(10))  BEGIN
	select moduleid, section, sectionname, type, name, timecreated from t_resource_access_course where moduleid not in 
	(select distinct moduleid from t_resource_access where userid=var_userid and courseid=var_courseid
	) and courseid=var_courseid and type!='quiz' order by timecreated desc;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_questionCorrectPercent` (`var_quizid` BIGINT(10))  BEGIN
	select t.questionid, qe.name questionname, t.correctpercent, qs.questionsummary from (select quizid, questionid, round(sum(correct)/count(*)*100,1) correctpercent from t_question_first_correct where quizid=var_quizid group by quizid, questionid) t
	join t_question_summary qs on t.questionid=qs.questionid
	join moodle.mdl_question qe on t.questionid=qe.id;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_split_siteadmin` ()  BEGIN
	DECLARE var_siteadmin varchar(255);
	DECLARE counts int;
	DECLARE i int;

	select value into var_siteadmin from moodle.mdl_config where name='siteadmins';
	set counts := length(var_siteadmin) - length(replace(var_siteadmin,',',''))+1;
	set i := 1;
	WHILE i <= counts DO
		insert into t_user_enrol_course(userid, firstname, lastname, roleid, rolename) select u.id, u.firstname, u.lastname, 0, 'globalmanager' from moodle.mdl_user u where u.id=substring_index(substring_index(var_siteadmin, ',', i), ',', -1);
		set i:= i+1;
	END WHILE;
	COMMIT;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_student_summary` (`p_courseid` INT)  BEGIN
	select uec.userid, uec.firstname, uec.lastname, a.counts course_counts, b.counts assign_counts, c.counts quiz_counts, d.counts post_counts 
	from t_user_enrol_course uec
	left join (select userid, count(distinct courseid) counts from t_user_enrol_course where userid in (select userid from t_user_enrol_course where courseid=p_courseid and roleid=5) group by userid) a on uec.userid=a.userid
	left join (select uec.userid, count(*) counts
	from t_user_enrol_course uec
	join v_assign_submission sub on uec.userid=sub.userid and uec.courseid=sub.courseid
	where uec.courseid=p_courseid and uec.roleid=5
	group by uec.userid) b on uec.userid=b.userid
	left join (select uec.userid, count(*) counts
	from t_user_enrol_course uec
	join v_quiz_grade qg on uec.userid=qg.userid and uec.courseid=qg.courseid
	where uec.courseid=p_courseid and uec.roleid=5
	group by uec.userid) c on uec.userid=c.userid
	left join (select uec.userid, count(*) counts
	from t_user_enrol_course uec
	join v_post_list pl on uec.userid=pl.userid and uec.courseid=pl.courseid
	where uec.courseid=p_courseid and uec.roleid=5
	group by uec.userid) d on uec.userid=d.userid
	where uec.courseid=p_courseid and roleid=5;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_teacher_summary` (`p_categoryid` INT)  BEGIN
	select distinct uec.userid, uec.firstname, uec.lastname, a.counts course_counts, b.counts student_counts, c.counts module_counts
	from t_user_enrol_course uec
	left join (select uec.userid, uec.firstname, uec.lastname, count(distinct courseid) counts
	from t_user_enrol_course uec where uec.roleid=3 and uec.categoryid=p_categoryid
	group by uec.userid) a on uec.userid=a.userid
	left join (select uec.userid, uec.firstname, uec.lastname, count(distinct uecs.userid) counts
	from t_user_enrol_course uec
	join t_user_enrol_course uecs on uec.courseid=uecs.courseid and uecs.roleid=5
	where uec.roleid=3 and uec.categoryid=p_categoryid
	group by uec.userid) b on uec.userid=b.userid
	left join t_teacher_module_counts c on uec.userid=c.userid
	where uec.roleid=3 and uec.categoryid=p_categoryid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_total_point` (`var_courseid` BIGINT(10), `var_threshold` FLOAT)  BEGIN
	select 
	userid, firstname, lastname,
	case when spendtimepoints<avgspendtimepoints*var_threshold then 1 else 0 end spendtimestate,
	case when quizcompleted=1 then case when quizpoints<avgquizpoints*var_threshold then 1 else 0 end else 2 end quizstate,
	case when assignmentpoints<avgassignmentpoints*var_threshold then 1 else 0 end assignmentstate
	from t_total_point t join moodle.mdl_user u on t.userid=u.id where courseid=var_courseid and totalpoints<avgtotalpoints*var_threshold;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `f_videoAccess` (`courseid` INT)  BEGIN	
	select t.courseid, t.coursename, t.moduleid, t.resourcename, count(distinct ta.userid) usercounts, sum(ta.logcounts) logcounts
	from (select c.id courseid, c.fullname coursename, cm.id moduleid, r.name resourcename
	from moodle.mdl_files f
	join moodle.mdl_context con on f.contextid=con.id
	join moodle.mdl_course_modules cm on con.instanceid=cm.id
	join moodle.mdl_course c on cm.course=c.id
	join moodle.mdl_resource r on cm.instance=r.id
	where substring(f.mimetype,1,5)='video' and c.id=courseid) t
	left join (select * from t_activetime_individual where type='resource' and courseid=courseid) ta on t.moduleid=ta.moduleid and t.courseid=ta.courseid
	group by t.courseid, t.moduleid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_activetime_course` ()  BEGIN
	truncate table t_activetime_course;
	insert into t_activetime_course select courseid, type, section, moduleid, resourcename, sum(spendtime) spendtime, sum(logcounts) logcounts from t_activetime_individual group by type, moduleid;
	REPAIR TABLE t_activetime_course QUICK;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_activetime_forum_discussions` ()  BEGIN
	truncate table t_activetime_forum_discussions;
	insert into t_activetime_forum_discussions 
	select t.moduleid, t.courseid, t.forumname, t.discussionid, t.discussionname, sum(t.counts) counts from(
	select cm.id moduleid, f.course courseid, f.name forumname, fd.id discussionid, fd.name discussionname, count(*) counts from moodle.mdl_logstore_standard_log sl join moodle.mdl_forum_discussions fd on sl.objectid=fd.id join moodle.mdl_forum f on fd.forum=f.id join moodle.mdl_course_modules cm on f.course=cm.course and cm.module=9 and cm.instance=f.id where sl.objecttable='forum_discussions' group by fd.id
	union
	select cm.id moduleid, f.course courseid, f.name forumname, fd.id discussionid, fd.name discussionname, count(*) counts from moodle.mdl_logstore_standard_log sl join moodle.mdl_forum_posts fp on sl.objectid=fp.id join moodle.mdl_forum_discussions fd on fp.discussion=fd.id join moodle.mdl_forum f on fd.forum=f.id join moodle.mdl_course_modules cm on f.course=cm.course and cm.module=9 and cm.instance=f.id where sl.objecttable='forum_posts' group by fd.id
	union 
	select cm.id moduleid, f.course courseid, f.name forumname, null, null, count(*) counts from moodle.mdl_logstore_standard_log sl join moodle.mdl_forum f on sl.objectid=f.id join moodle.mdl_course_modules cm on f.course=cm.course and cm.module=9 and cm.instance=f.id where sl.objecttable='forum' group by moduleid
	) t group by t.moduleid, t.discussionid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_activetime_individual` ()  BEGIN
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=16 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select q.name from moodle.mdl_quiz q where q.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=16), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=16) where type='quiz';
	update t_activetime_individual_m t set type='quiz', t.resourceid=(select q.id from moodle.mdl_quiz_attempts qa join moodle.mdl_quiz q on qa.quiz=q.id where qa.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=16 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select q.name from moodle.mdl_quiz q where q.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=16), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=16) where type='quiz_attempts';
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=8 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select f.name from moodle.mdl_folder f where f.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=8), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=8) where type='folder';
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=17 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select r.name from moodle.mdl_resource r where r.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=17), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=17) where type='resource';
#forum
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=9 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select f.name from moodle.mdl_forum f where f.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9) where type='forum';
	update t_activetime_individual_m t set type='forum', t.resourceid=(select f.id from moodle.mdl_forum f join moodle.mdl_forum_discussions fd on fd.forum=f.id where fd.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=9 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select f.name from moodle.mdl_forum f where f.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9) where type='forum_discussions';
	update t_activetime_individual_m t set type='forum', t.resourceid=(select f.id from moodle.mdl_forum f join moodle.mdl_forum_discussion_subs fd on fd.forum=f.id where fd.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=9 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select f.name from moodle.mdl_forum f where f.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9) where type='forum_discussion_subs';
	update t_activetime_individual_m t set type='forum', t.resourceid=(select f.id from moodle.mdl_forum f join moodle.mdl_forum_subscriptions fs on fs.forum=f.id where fs.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=9 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select f.name from moodle.mdl_forum f where f.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=9) where type='forum_subscriptions';
#book
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=3 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select b.name from moodle.mdl_book b where b.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=3), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=3) where type='book';
	update t_activetime_individual_m t set type='book', t.resourceid=(select b.id from moodle.mdl_book b join moodle.mdl_book_chapters bc on bc.bookid=b.id where bc.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=3 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select b.name from moodle.mdl_book b where b.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=3), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=3) where type='book_chapters';
#data
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=6 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select d.name from moodle.mdl_data d where d.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=6), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=6) where type='data';
	update t_activetime_individual_m t set type='data', t.resourceid=(select d.id from moodle.mdl_data d join moodle.mdl_data_fields df on df.dataid=d.id where df.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=6 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select d.name from moodle.mdl_data d where d.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=6), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=6) where type='data_fields';
	update t_activetime_individual_m t set type='data', t.resourceid=(select d.id from moodle.mdl_data d join moodle.mdl_data_records df on df.dataid=d.id where df.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=6 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select d.name from moodle.mdl_data d where d.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=6), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=6) where type='data_records';
#assign
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=1 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_assign a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1) where type='assign';
	update t_activetime_individual_m t set type='assign', t.resourceid=(select a.id from moodle.mdl_assign a join moodle.mdl_assign_submission sub on sub.assignment=a.id where sub.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=1 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_assign a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1) where type='assign_submission';
	update t_activetime_individual_m t set type='assign', t.resourceid=(select a.id from moodle.mdl_assign a join moodle.mdl_assignsubmission_file sub on sub.assignment=a.id where sub.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=1 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_assign a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1) where type='assignsubmission_file';
	update t_activetime_individual_m t set type='assign', t.resourceid=(select a.id from moodle.mdl_assign a join moodle.mdl_assignsubmission_onlinetext sub on sub.assignment=a.id where sub.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=1 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_assign a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=1) where type='assignsubmission_onlinetext';
#chat
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=4 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select c.name from moodle.mdl_chat c where c.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=4), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=4) where type='chat';
	update t_activetime_individual_m t set type='chat', t.resourceid=(select c.id from moodle.mdl_chat c join moodle.mdl_chat_messages mes on mes.chatid=c.id where mes.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=4 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select c.name from moodle.mdl_chat c where c.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=4), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=4) where type='chat_messages';
#choice
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=5 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select c.name from moodle.mdl_choice c where c.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=5), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=5) where type='choice';
	update t_activetime_individual_m t set type='choice', t.resourceid=(select c.id from moodle.mdl_choice c join moodle.mdl_choice_answers ca on ca.choiceid=c.id where ca.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=5 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select c.name from moodle.mdl_choice c where c.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=5), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=5) where type='choice_answers';
#glossary
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=10 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select g.name from moodle.mdl_glossary g where g.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=10), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=10) where type='glossary';
	update t_activetime_individual_m t set type='glossary', t.resourceid=(select g.id from moodle.mdl_glossary g join moodle.mdl_glossary_entries ge on ge.glossaryid=g.id where ge.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=10 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select g.name from moodle.mdl_glossary g where g.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=10), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=10) where type='glossary_entries';
#lesson
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=13 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select l.name from moodle.mdl_lesson l where l.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=13), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=13) where type='lesson';
	update t_activetime_individual_m t set type='lesson', t.resourceid=(select l.id from moodle.mdl_lesson l join moodle.mdl_lesson_pages lp on lp.lessonid=l.id where lp.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=13 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select l.name from moodle.mdl_lesson l where l.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=13), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=13) where type='lesson_pages';
#attendance
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=23 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_attendance a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=23), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=23) where type='attendance';
	update t_activetime_individual_m t set type='attendance', t.resourceid=(select a.id from moodle.mdl_attendance a join moodle.mdl_attendance_sessions ses on ses.attendanceid=a.id where ses.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=23 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_attendance a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=23), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=23) where type='attendance_sessions';
	update t_activetime_individual_m t set type='attendance', t.resourceid=(select a.id from moodle.mdl_attendance a join moodle.mdl_attendance_statuses ses on ses.attendanceid=a.id where ses.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=23 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select a.name from moodle.mdl_attendance a where a.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=23), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=23) where type='attendance_statuses';
#scorm
	update t_activetime_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=18 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select s.name from moodle.mdl_scorm s where s.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=18), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=18) where type='scorm';
	update t_activetime_individual_m t set type='scorm', t.resourceid=(select s.id from moodle.mdl_scorm s join moodle.mdl_scorm_scoes ss on ss.scorm=s.id where ss.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=18 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select s.name from moodle.mdl_scorm s where s.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=18), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=18) where type='scorm_scoes';
	update t_activetime_individual_m t set type='scorm', t.resourceid=(select s.id from moodle.mdl_scorm s join moodle.mdl_scorm_scoes_track sst on sst.scormid=s.id where sst.id=t.resourceid), t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=18 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select s.name from moodle.mdl_scorm s where s.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=18), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=18) where type='scorm_scoes_track';
#后续处理，删除为空的，写入正式表
	delete from t_activetime_individual_m where resourcename is null;
	truncate t_activetime_individual;
	insert into t_activetime_individual select id, courseid, userid, type, section, sectionname, moduleid, resourcename, sum(spendtime) spendtime, sum(logcounts) logcounts from t_activetime_individual_m group by courseid, userid, type, moduleid;
	REPAIR TABLE t_activetime_individual QUICK;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_activetime_individual_logcounts` ()  BEGIN
	truncate table t_activetime_individual_logcounts;
	insert into t_activetime_individual_logcounts select courseid, userid, type, resourceid, sum(logcounts) counts from t_activetime_individual_m group by courseid, userid, type, resourceid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_activetime_individual_m` ()  proc:BEGIN
#使用变量声明
	DECLARE var_course_id INT;
	DECLARE var_course_name VARCHAR(64);
	DECLARE var_user_id INT;
	DECLARE var_action VARCHAR(16);
	DECLARE var_objecttable VARCHAR(50);
	DECLARE var_object_id BIGINT(10);
	DECLARE var_time_created INT;
	DECLARE prev_time_created INT DEFAULT -1;
	DECLARE prev_user_id INT DEFAULT -1;
	DECLARE prev_course_id INT DEFAULT -1;
	DECLARE counts INT;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur_loggedin BIGINT DEFAULT 1;
	DECLARE var_loggedin BIGINT DEFAULT 0;

	DECLARE prev_objecttable VARCHAR(50);
	DECLARE prev_object_id BIGINT(10);
	DECLARE cur_object_name VARCHAR(255);
	DECLARE prev_id BIGINT(10);
	DECLARE cur_id BIGINT(10);

	DECLARE max_id BIGINT(10);
	DECLARE var_begin_time BIGINT(20);
	DECLARE var_end_time BIGINT(20);

	DECLARE i BIGINT(10);
	DECLARE a BIGINT(10);
	DECLARE b BIGINT(10);
	DECLARE c BIGINT(10);
#游标相关声明，按照增量表统计相应日期内的记录
	DECLARE ref_cur CURSOR FOR (select sl.courseid, c.fullname, sl.userid, sl.action, sl.objecttable, sl.objectid, sl.timecreated from moodle.mdl_logstore_standard_log sl left join moodle.mdl_course c on sl.courseid=c.id where (objecttable='quiz' or objecttable='quiz_attempts' or objecttable='folder' or objecttable='resource' or action='loggedin' or objecttable='forum' or objecttable='forum_discussions' or objecttable='forum_discussion_subs' or objecttable='forum_subscriptions' or objecttable='assignsubmission_file' or objecttable='assign' or objecttable='assignsubmission_onlinetext' or objecttable='assign_submission' or objecttable='chat' or objecttable='chat_messages' or objecttable='choice' or objecttable='choice_answers' or objecttable='glossary' or objecttable='glossary_entries' or objecttable='lesson' or objecttable='lesson_pages' or objecttable='data' or objecttable='data_fields' or objecttable='data_records' or objecttable='book' or objecttable='book_chapters' or objecttable='attendance' or objecttable='attendance_sessions' or objecttable='attendance_statuses' or objecttable='scorm' or objecttable='scorm_scoes' or objecttable='scorm_scoes_track') and sl.timecreated > var_begin_time and sl.timecreated <= var_end_time order by sl.userid, sl.timecreated);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
#先获取增量的日期信息
	select max(id) into max_id from t_activetime_increment;
	select max(begintime) into var_begin_time from t_activetime_increment where id=(select max(id) from t_activetime_increment) and status='waiting';
	select max(endtime) into var_end_time from t_activetime_increment where id=(select max(id) from t_activetime_increment) and status='waiting';
#检查增量日期是否存在，不存在说明执行错误，跳出过程。
	IF var_begin_time is null or var_end_time is null THEN
		insert into t_activetime_increment(completetime, status) values(unix_timestamp(sysdate()), 'no waiting time');
		COMMIT;
		LEAVE proc;
	ELSE
		update t_activetime_increment set status='executing' where id=max_id;
		COMMIT;
		select from_unixtime(var_begin_time, '%Y-%m-%d %h:%i:%s'), from_unixtime(var_end_time, '%Y-%m-%d %h:%i:%s');
	END IF;
	select 1, sysdate();
#主程序处理
	OPEN ref_cur;
	cur_loop:LOOP
		FETCH ref_cur INTO var_course_id, var_course_name, var_user_id, var_action, var_objecttable, var_object_id, var_time_created;
		IF done THEN
			LEAVE cur_loop;
		END IF;
		IF strcmp(var_action, 'loggedin')!=0 and prev_course_id=var_course_id and prev_user_id=var_user_id THEN
			#objecttable转化成type
			IF var_object_id = prev_object_id and var_objecttable = prev_objecttable THEN
				update t_activetime_individual_m set spendtime = spendtime + (var_time_created-prev_time_created), logcounts = logcounts + 1 where id=prev_id;
			ELSE
				select max(id) into cur_id from t_activetime_individual_m where userid=var_user_id and courseid=var_course_id and type=var_objecttable and resourceid=var_object_id;
				IF cur_id is null THEN
					insert into t_activetime_individual_m(courseid,userid,type,resourceid,resourcename, spendtime,logcounts) values(var_course_id, var_user_id, var_objecttable, var_object_id, NULL, var_time_created-prev_time_created, 1);
				ELSE
					update t_activetime_individual_m set spendtime = spendtime + (var_time_created-prev_time_created), logcounts = logcounts + 1 where id=cur_id;
				END IF;
			END IF;
		END IF;
		set prev_user_id = var_user_id;
		set prev_time_created = var_time_created;
		set prev_course_id = var_course_id;
		set prev_objecttable = var_objecttable;
		set prev_object_id = var_object_id;
		set prev_id = cur_id;
		set done = 0;
	END LOOP;
	CLOSE ref_cur;
#写入成功状态和完成时间
	update t_activetime_increment set status='success', completetime=unix_timestamp(sysdate()) where id=max_id;
	insert into t_activetime_increment(begintime, endtime, status) values(var_end_time, var_end_time + 1*24*60*60, 'waiting');
	COMMIT;
	select 2,sysdate();
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_activetime_resource` ()  BEGIN
	truncate table t_activetime_resource;
	insert into t_activetime_resource
		select courseid, type, section, sectionname, moduleid, resourcename, sum(spendtime) spendtime, sum(logcounts) logcounts from t_activetime_individual where (type='resource' or type='folder') group by courseid, type, moduleid; 
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_active_course` ()  BEGIN
#使用变量声明
	DECLARE var_course_id INT;
	DECLARE var_course_name VARCHAR(64);
	DECLARE var_user_id INT;
	DECLARE var_action VARCHAR(16);
	DECLARE var_time_created INT;
	DECLARE prev_time_created INT DEFAULT -1;
	DECLARE prev_user_id INT DEFAULT -1;
	DECLARE prev_course_id INT DEFAULT -1;
	DECLARE counts INT;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur_loggedin BIGINT DEFAULT 1;
	DECLARE var_loggedin BIGINT DEFAULT 0;
#游标相关声明
	DECLARE ref_cur CURSOR FOR (select sl.courseid, c.fullname, sl.userid, sl.action, sl.timecreated from moodle.mdl_logstore_standard_log sl left join moodle.mdl_course c on sl.courseid=c.id order by userid, timecreated);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
#主程序处理
	select sysdate();
#清空之前统计的数据，重新统计基础课程数据
	TRUNCATE TABLE t_active_course;
	insert into t_active_course select cc.id categoryid,cc.name categoryname,uec.courseid, c.fullname coursename,count(*) usercounts,cast(0 as unsigned) activetime,cast(0 as unsigned) activetimes,cast(0 as decimal(10,1)) avgactivetime,cast(0 as decimal(10,1)) avgactivetimes,cast(0 as unsigned) curloggedin,cast(0 as unsigned) accesstimes,cast(0 as unsigned) avgaccesstimes from t_user_enrol_course uec join moodle.mdl_course c on uec.courseid=c.id join moodle.mdl_course_categories cc on c.category=cc.id group by courseid;
	OPEN ref_cur;
	cur_loop:LOOP
		FETCH ref_cur INTO var_course_id, var_course_name, var_user_id, var_action, var_time_created;
		IF done THEN
			LEAVE cur_loop;
		END IF;
		select count(*) into counts from t_active_course where courseid=var_course_id;
		IF strcmp(var_action, 'loggedin')=0 THEN
			set cur_loggedin = cur_loggedin + 1;
		ELSEIF prev_course_id!=-1 and counts!=0 THEN
			select curloggedin into var_loggedin from t_active_course where courseid=var_course_id;
			update t_active_course set activetimes = activetimes + 1 where courseid=var_course_id;
			IF var_user_id = prev_user_id THEN
				update t_active_course set activetime = activetime + (var_time_created - prev_time_created) where courseid=prev_course_id;
			END IF;
			IF var_loggedin!=cur_loggedin THEN
				update t_active_course set accesstimes=accesstimes+1, curloggedin=cur_loggedin where courseid=var_course_id;
			END IF;
		END IF;
		set prev_user_id = var_user_id;
		set prev_time_created = var_time_created;
		set prev_course_id = var_course_id;
	END LOOP;
	CLOSE ref_cur;
	update t_active_course set avgactivetime = round(activetime/NULLIF(usercounts,0),1);
	update t_active_course set avgactivetimes = round(activetimes/NULLIF(usercounts,0),1);
	update t_active_course set avgaccesstimes = round(avgactivetime/NULLIF(avgactivetimes,0),1);
	COMMIT;
	select sysdate();
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_active_individual` ()  BEGIN
#使用变量声明
	DECLARE var_course_id INT;
	DECLARE var_course_name VARCHAR(64);
	DECLARE var_user_id INT;
	DECLARE var_action VARCHAR(16);
	DECLARE var_objecttable VARCHAR(50);
	DECLARE var_object_id BIGINT(10);
	DECLARE var_time_created INT;
	DECLARE prev_time_created INT DEFAULT -1;
	DECLARE prev_user_id INT DEFAULT -1;
	DECLARE prev_course_id INT DEFAULT -1;
	DECLARE counts INT;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur_loggedin BIGINT DEFAULT 1;
	DECLARE var_loggedin BIGINT DEFAULT 0;

	DECLARE prev_objecttable VARCHAR(16);
	DECLARE prev_object_id BIGINT(10);
	DECLARE cur_object_name VARCHAR(255);
	DECLARE prev_id BIGINT(10);
	DECLARE cur_id BIGINT(10);
#游标相关声明
	DECLARE ref_cur CURSOR FOR (select sl.courseid, c.fullname, sl.userid, sl.action, sl.objecttable, sl.objectid, sl.timecreated from moodle.mdl_logstore_standard_log sl left join moodle.mdl_course c on sl.courseid=c.id where (objecttable='quiz' or objecttable='quiz_attempts' or objecttable='folder' or objecttable='resource') or action='loggedin' order by sl.userid, sl.timecreated);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
#主程序处理
	select 1, sysdate();
#清空之前统计的数据，重新统计基础课程数据
	TRUNCATE TABLE t_active_individual;
	OPEN ref_cur;
	cur_loop:LOOP
		FETCH ref_cur INTO var_course_id, var_course_name, var_user_id, var_action, var_objecttable, var_object_id, var_time_created;
		IF done THEN
			LEAVE cur_loop;
		END IF;
		IF strcmp(var_action, 'loggedin')!=0 and prev_course_id=var_course_id and prev_user_id=var_user_id THEN
			#objecttable转化成type
			IF var_object_id = prev_object_id and var_objecttable = prev_objecttable THEN
				update t_active_individual set spendtime = spendtime + (var_time_created-prev_time_created), logcounts = logcounts + 1 where id=prev_id;
			ELSE
				select id into cur_id from t_active_individual where userid=var_user_id and courseid=var_course_id and type=var_objecttable and resourceid=var_object_id;
				IF done THEN
					insert into t_active_individual(courseid,userid,type,resourceid,resourcename, spendtime,logcounts) values(var_course_id, var_user_id, var_objecttable, var_object_id, NULL, var_time_created-prev_time_created, 1);
					set done = 0;
				ELSE
					update t_active_individual set spendtime = spendtime + (var_time_created-prev_time_created), logcounts = logcounts + 1 where id=cur_id;
				END IF;
			END IF;
		END IF;
		set prev_user_id = var_user_id;
		set prev_time_created = var_time_created;
		set prev_course_id = var_course_id;
		set prev_objecttable = var_objecttable;
		set prev_object_id = var_object_id;
		set prev_id = cur_id;
	END LOOP;
	CLOSE ref_cur;
	COMMIT;
	select 2,sysdate();
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_course_statistic` ()  BEGIN
	insert into t_course_statistic
	select r.year year, r.typea, r.typeb, c.id courseid, c.fullname coursename, uec.counts s_counts, a.counts video_counts, b.counts resource_counts, d.counts ppt_counts, e.counts pdf_counts, case when strcmp(c.summary, '')=1 then '有' else '无' end summary, f.counts assign_counts, g.spendtime s_spentime, h.spendtime t_spendtime, i.counts video_access_counts, j.counts activity_counts, k.counts s_discussion_counts, l.counts s_post_counts, m.counts quiz_counts, n.counts question_counts, p.counts assign_sub_counts, q.counts assign_grade_counts
	from moodle.mdl_course c
	left join (#学生数
		select uec.courseid, count(*) counts from t_user_enrol_course uec where roleid=5 group by uec.courseid
	) uec on c.id=uec.courseid
	left join (#视频
		select c.id, count(*) counts
		from moodle.mdl_files f
		join moodle.mdl_context con on f.contextid=con.id
		join moodle.mdl_course_modules cm on con.instanceid=cm.id
		join moodle.mdl_course c on cm.course=c.id
		where substring(f.mimetype,1,5)='video'	group by c.id
		order by c.id) a on a.id=c.id
	left join (#资源总数
		select c.id, ifnull(a.counts,0)+ifnull(b.counts,0)+ifnull(d.counts,0)+ifnull(e.counts,0) counts from
		moodle.mdl_course c 
		left join (select course, count(*) counts from moodle.mdl_resource group by course) a on a.course=c.id
		left join (select course, count(*) counts from moodle.mdl_page group by course) b on b.course=c.id
		left join (select course, count(*) counts from moodle.mdl_url group by course) d on d.course=c.id
		left join 
		(select cm.course, count(*) counts
		from moodle.mdl_files f 
		join moodle.mdl_context con on f.contextid=con.id
		join moodle.mdl_course_modules cm on con.instanceid=cm.id
		join moodle.mdl_folder fo on cm.instance=fo.id and cm.course=fo.course
		where f.filesize!=0
		group by cm.course) e on e.course=c.id
	) b on b.id=c.id
	left join (#PPT
		select cm.course, count(*) counts
		from moodle.mdl_files f 
		join moodle.mdl_context con on f.contextid=con.id
		join moodle.mdl_course_modules cm on con.instanceid=cm.id
		where f.mimetype='application/vnd.ms-powerpoint'
		group by cm.course
	) d on d.course=c.id
	left join (#PDF
		select cm.course, count(*) counts
		from moodle.mdl_files f 
		join moodle.mdl_context con on f.contextid=con.id
		join moodle.mdl_course_modules cm on con.instanceid=cm.id
		where f.mimetype='application/pdf'
		group by cm.course
	) e on e.course=c.id
	left join (#作业
		select a.course, count(*) counts
		from moodle.mdl_assign a group by course
	) f on f.course=c.id
	left join (#学生使用总时长（小时）
		select ta.courseid, round(sum(spendtime)/3600,1) spendtime
		from t_activetime_individual ta
		join t_user_enrol_course uec on ta.userid=uec.userid and ta.courseid=uec.courseid
		where uec.roleid=5
		group by ta.courseid
	) g on g.courseid=c.id
	left join (#教师使用总时长（小时）
		select ta.courseid, round(sum(spendtime)/3600,1) spendtime
		from t_activetime_individual ta
		join t_user_enrol_course uec on ta.userid=uec.userid and ta.courseid=uec.courseid
		where uec.roleid=3 or uec.roleid=9
		group by ta.courseid
	) h on h.courseid=c.id
	left join (#视频总点击数
		select c.id, sl.counts
		from (select h.*
		from moodle.mdl_files f
		left join moodle.mdl_context con on f.contextid=con.id
		left join moodle.mdl_course_modules cm on con.instanceid=cm.id
		left join moodle.mdl_resource h on cm.instance=h.id and cm.course=h.course
		where substring(f.mimetype,1,5)='video' and h.id is not null) t
		left join (select sl.objectid, count(*) counts, count(distinct userid) user_counts from moodle.mdl_logstore_standard_log sl where sl.objecttable='resource' and sl.action='viewed' group by sl.objectid) sl on t.id=sl.objectid 
		left join moodle.mdl_course c on t.course=c.id
		group by c.id
	) i on i.id=c.id
	left join (#活动总数
		select c.id, ifnull(f.counts,0)+ifnull(a.counts,0)+ifnull(q.counts,0)+ifnull(ch.counts,0)+ifnull(qn.counts,0) counts
		from moodle.mdl_course c
		left join (select f.course, count(*) counts from moodle.mdl_forum f group by course) f on f.course=c.id
		left join (select course, count(*) counts from moodle.mdl_assign group by course) a on a.course=c.id
		left join (select course, count(*) counts from moodle.mdl_quiz group by course) q on q.course=c.id
		left join (select course, count(*) counts from moodle.mdl_chat group by course) ch on ch.course=c.id
		left join (select course, count(*) counts from moodle.mdl_questionnaire qn group by course) qn on qn.course=c.id
		where c.visible=1
		order by c.id
	) j on c.id=j.id
	left join (#学生发起讨论次数
		select uec.courseid, count(*) counts
		from moodle.mdl_forum_discussions fd 
		join moodle.mdl_forum f on fd.forum=f.id 
		join t_user_enrol_course uec on fd.userid=uec.userid and f.course=uec.courseid 
		join moodle.mdl_user u on uec.userid=u.id
		where uec.roleid=5
		group by uec.courseid
	) k on c.id=k.courseid
	left join (#学生的发帖数
		select uec.courseid, count(*) counts
		from moodle.mdl_forum_posts fp 
		join moodle.mdl_forum_discussions fd on fp.discussion=fd.id
		join moodle.mdl_forum f on fd.forum=f.id 
		join t_user_enrol_course uec on fd.userid=uec.userid and f.course=uec.courseid 
		join moodle.mdl_user u on uec.userid=u.id
		where uec.roleid=5
		group by uec.courseid
	) l on c.id=l.courseid
	left join (#测试
		select q.course, count(*) counts from moodle.mdl_quiz q group by q.course
	) m on c.id=m.course
	left join (#测试题目数
		select c.id, count(*) counts
		from moodle.mdl_quiz_slots qs
		join moodle.mdl_quiz q on qs.quizid=q.id
		join moodle.mdl_course c on q.course=c.id
		group by c.id
	) n on c.id=n.id
	left join (#作业
		select a.course, count(*) counts from moodle.mdl_assign a group by a.course
	) o on c.id=o.course
	left join (#提交作业数
		select c.id, count(distinct concat(sub.userid, sub.assignment)) counts
		from moodle.mdl_assign_submission sub
		join moodle.mdl_assign a on sub.assignment=a.id
		join moodle.mdl_course c on a.course=c.id
		group by c.id
	) p on c.id=p.id
	left join (#作业批改数
		select a.course, count(distinct concat(ag.userid, ag.assignment)) counts
		from moodle.mdl_assign_grades ag
		join moodle.mdl_assign a on ag.assignment=a.id
		group by a.course
	) q on c.id=q.course
	join (#tag信息
		select c.id, ti.name, substring(ti.name,1,locate('年',ti.name)-1) year, substring(ti.name,locate('年',ti.name)+1,length(ti.name)-locate('年',ti.name)-1) typea, tii.name typeb
		from (select ti.*, t.name from moodle.mdl_tag_instance ti join moodle.mdl_tag t on ti.tagid=t.id where t.name like '%年%' or t.name='自建课程') ti
		join (select ti.*, t.name from moodle.mdl_tag_instance ti join moodle.mdl_tag t on ti.tagid=t.id where t.name not like '%年%' and t.name!='自建课程') tii on ti.contextid=tii.contextid
		join moodle.mdl_context con on ti.contextid=con.id
		join moodle.mdl_course c on con.instanceid=c.id
	) r on c.id=r.id;

	COMMIT;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_latest_forum_discussions` ()  BEGIN
	truncate table t_latest_forum_discussions;
	insert into t_latest_forum_discussions
	select t.moduleid, t.courseid, t.forumname, t.discussionid, t.discussionname, sum(t.counts) counts from(
	select cm.id moduleid, f.course courseid, f.name forumname, fd.id discussionid, fd.name discussionname, count(*) counts from moodle.mdl_logstore_standard_log sl join moodle.mdl_forum_discussions fd on sl.objectid=fd.id join moodle.mdl_forum f on fd.forum=f.id join moodle.mdl_course_modules cm on f.course=cm.course and cm.module=9 and cm.instance=f.id where sl.objecttable='forum_discussions' and sl.timecreated >= unix_timestamp(date_add(sysdate(), interval -2 week)) group by fd.id
	union
	select cm.id moduleid, f.course courseid, f.name forumname, fd.id discussionid, fd.name discussionname, count(*) counts from moodle.mdl_logstore_standard_log sl join moodle.mdl_forum_posts fp on sl.objectid=fp.id join moodle.mdl_forum_discussions fd on fp.discussion=fd.id join moodle.mdl_forum f on fd.forum=f.id join moodle.mdl_course_modules cm on f.course=cm.course and cm.module=9 and cm.instance=f.id where sl.objecttable='forum_posts' and sl.timecreated >= unix_timestamp(date_add(sysdate(), interval -2 week)) group by fd.id
	union
	select cm.id moduleid, f.course courseid, f.name forumname, null, null, count(*) counts from moodle.mdl_logstore_standard_log sl join moodle.mdl_forum f on sl.objectid=f.id join moodle.mdl_course_modules cm on f.course=cm.course and cm.module=9 and cm.instance=f.id where sl.objecttable='forum' and sl.timecreated >= unix_timestamp(date_add(sysdate(), interval -2 week)) group by moduleid
	) t group by t.moduleid, t.discussionid;
	commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_latest_individual_m` ()  proc:BEGIN
#使用变量声明
	DECLARE var_course_id INT;
	DECLARE var_course_name VARCHAR(64);
	DECLARE var_user_id INT;
	DECLARE var_action VARCHAR(16);
	DECLARE var_objecttable VARCHAR(50);
	DECLARE var_object_id BIGINT(10);
	DECLARE var_time_created INT;
	DECLARE prev_time_created INT DEFAULT -1;
	DECLARE prev_user_id INT DEFAULT -1;
	DECLARE prev_course_id INT DEFAULT -1;
	DECLARE counts INT;
	DECLARE done INT DEFAULT FALSE;
	DECLARE cur_loggedin BIGINT DEFAULT 1;
	DECLARE var_loggedin BIGINT DEFAULT 0;

	DECLARE prev_objecttable VARCHAR(50);
	DECLARE prev_object_id BIGINT(10);
	DECLARE cur_object_name VARCHAR(255);
	DECLARE prev_id BIGINT(10);
	DECLARE cur_id BIGINT(10);

	DECLARE max_id BIGINT(10);
	DECLARE var_begin_time BIGINT(20);
	DECLARE var_end_time BIGINT(20);
#游标相关声明，按照增量表统计相应日期内的记录
	DECLARE ref_cur CURSOR FOR (select sl.courseid, c.fullname, sl.userid, sl.action, sl.objecttable, sl.objectid, sl.timecreated from moodle.mdl_logstore_standard_log sl left join moodle.mdl_course c on sl.courseid=c.id where (objecttable='quiz' or objecttable='quiz_attempts' or objecttable='folder' or objecttable='resource' or action='loggedin' or objecttable='forum' or objecttable='forum_discussions' or objecttable='forum_discussion_subs' or objecttable='forum_subscriptions' or objecttable='assignsubmission_file' or objecttable='assign' or objecttable='assignsubmission_onlinetext' or objecttable='assign_submission' or objecttable='chat' or objecttable='chat_messages' or objecttable='choice' or objecttable='choice_answers' or objecttable='glossary' or objecttable='glossary_entries' or objecttable='lesson' or objecttable='lesson_pages' or objecttable='data' or objecttable='data_fields' or objecttable='data_records' or objecttable='book' or objecttable='book_chapters' or objecttable='attendance' or objecttable='attendance_sessions' or objecttable='attendance_statuses' or objecttable='scorm' or objecttable='scorm_scoes' or objecttable='scorm_scoes_track') and sl.timecreated > var_begin_time and sl.timecreated <= var_end_time order by sl.userid, sl.timecreated);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
#先获取增量的日期信息
	select max(timecreated) into var_end_time from moodle.mdl_logstore_standard_log;
	set var_begin_time := unix_timestamp(date_add(from_unixtime(var_end_time, '%Y-%m-%d'), interval -1 month));
	#set var_begin_time := unix_timestamp(date_add('2017-03-01', interval -1 month));
	#set var_end_time := unix_timestamp('2017-03-01');
	select from_unixtime(var_begin_time, '%Y-%m-%d %h:%i:%s'), from_unixtime(var_end_time, '%Y-%m-%d %h:%i:%s');
	select 1, sysdate();
#主程序处理
	truncate table t_latest_individual_m;
	OPEN ref_cur;
	cur_loop:LOOP
		FETCH ref_cur INTO var_course_id, var_course_name, var_user_id, var_action, var_objecttable, var_object_id, var_time_created;
		IF done THEN
			LEAVE cur_loop;
		END IF;
		IF strcmp(var_action, 'loggedin')!=0 and prev_course_id=var_course_id and prev_user_id=var_user_id THEN
			#objecttable转化成type
			IF var_object_id = prev_object_id and var_objecttable = prev_objecttable THEN
				update t_latest_individual_m set spendtime = spendtime + (var_time_created-prev_time_created), logcounts = logcounts + 1 where id=prev_id;
			ELSE
				select max(id) into cur_id from t_latest_individual_m where userid=var_user_id and courseid=var_course_id and type=var_objecttable and resourceid=var_object_id;
				IF cur_id is null THEN
					insert into t_latest_individual_m(courseid,userid,type,resourceid,resourcename, spendtime, logcounts) values(var_course_id, var_user_id, var_objecttable, var_object_id, NULL, var_time_created-prev_time_created, 1);
				ELSE
					update t_latest_individual_m set spendtime = spendtime + (var_time_created-prev_time_created), logcounts = logcounts + 1 where id=cur_id;
				END IF;
			END IF;
		END IF;
		set prev_user_id = var_user_id;
		set prev_time_created = var_time_created;
		set prev_course_id = var_course_id;
		set prev_objecttable = var_objecttable;
		set prev_object_id = var_object_id;
		set prev_id = cur_id;
		set done = 0;
	END LOOP;
	CLOSE ref_cur;
	COMMIT;
	select 2,sysdate();
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_latest_resource` ()  BEGIN
	update t_latest_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=8 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select f.name from moodle.mdl_folder f where f.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=8), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=8) where type='folder';
	update t_latest_individual_m t set t.moduleid=(select cm.id from moodle.mdl_course_modules cm where cm.module=17 and cm.course=t.courseid and cm.instance=t.resourceid), t.resourcename=(select r.name from moodle.mdl_resource r where r.id=t.resourceid), t.section=(select cs.section from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=17), t.sectionname=(select cs.name from moodle.mdl_course_modules cm join moodle.mdl_course_sections cs on cm.section=cs.id where cm.course=t.courseid and cm.instance=t.resourceid and cm.module=17) where type='resource';
#后续处理，删除为空的，写入正式表
	delete from t_latest_individual_m where resourcename is null;
	truncate t_latest_resource;
	insert into t_latest_resource select id, courseid, type, section, sectionname, moduleid, resourcename, sum(spendtime) spendtime, sum(logcounts) logcounts from t_latest_individual_m group by courseid, type, moduleid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_question_first_correct` ()  BEGIN
	truncate table t_question_first_correct;
	insert into t_question_first_correct select quizid, questionid, userid, correct, count(*) 
	from v_question_correct qc
	group by quizid, questionid, userid order by userid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_question_summary` ()  BEGIN
	truncate table t_question_summary;
	insert into t_question_summary select questionid, max(questionsummary) questionsummary from moodle.mdl_question_attempts group by questionid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_teacher_module_counts` ()  BEGIN
	TRUNCATE t_teacher_module_counts;
	INSERT INTO t_teacher_module_counts(userid,firstname,lastname,counts)
	SELECT uec.userid, uec.firstname, uec.lastname, COUNT(DISTINCT CONCAT(module, instance)) counts 
	FROM moodle.mdl_logstore_standard_log sl
	JOIN moodle.mdl_course_modules cm ON sl.objectid=cm.id
	JOIN t_user_enrol_course uec ON sl.userid=uec.userid
	JOIN moodle.mdl_course c ON sl.courseid=c.id
	JOIN moodle.mdl_course_categories cc ON c.category=c.category
	WHERE sl.action='created' AND sl.objecttable='course_modules' AND uec.roleid=3
	GROUP BY sl.userid;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_total_point` ()  BEGIN
	select sysdate();
	truncate table t_total_point;
	insert into t_total_point(courseid, userid) select courseid, userid from t_user_enrol_course where roleid=5;
	update t_total_point t set spendtimepoints=(select points from v_spendtime_point where courseid=t.courseid and userid=t.userid), avgspendtimepoints=(select round(avg(points),2) from v_spendtime_point where courseid=t.courseid);
	update t_total_point t set spendtimepoints=0 where spendtimepoints is null;
	update t_total_point t set quizpoints=(select points from v_quiz_point where courseid=t.courseid and userid=t.userid), avgquizpoints=(select round(avg(points),2) from v_quiz_point where courseid=t.courseid), quizcompleted=(select quizcompleted from v_quiz_point where courseid=t.courseid and userid=t.userid);
	update t_total_point t set quizpoints=0 where quizpoints is null;
	update t_total_point t set quizcompleted=0 where quizcompleted is null;
	update t_total_point t set assignmentpoints=(select points from v_assignment_point where courseid=t.courseid and userid=t.userid), avgassignmentpoints=(select round(avg(points),2) from v_assignment_point where courseid=t.courseid);
	update t_total_point t set assignmentpoints=0 where assignmentpoints is null;
	update t_total_point t set totalpoints=ifnull(spendtimepoints,0)+ifnull(quizpoints,0)+ifnull(assignmentpoints,0), avgtotalpoints=ifnull(avgspendtimepoints,0)+ifnull(avgquizpoints,0)+ifnull(avgassignmentpoints,0);
	select sysdate();
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` PROCEDURE `p_user_enrol_course` ()  BEGIN
	truncate table t_user_enrol_course;
	insert into t_user_enrol_course
	select t.userid, t.firstname, t.lastname, r.id roleid, r.shortname rolename, t.categoryid, t.idnumber, t.categoryname, t.courseid, t.coursename
	from
	(select u.id userid, u.firstname, u.lastname, ra.roleid, r.name rolename, cc.id categoryid, cc.idnumber, cc.name categoryname, c.id courseid, c.fullname coursename, min(r.sortorder) sortorder
	from moodle.mdl_role_assignments ra 
	join moodle.mdl_role r on ra.roleid=r.id
	join moodle.mdl_context ct on ra.contextid=ct.id
	join moodle.mdl_course c on ct.instanceid=c.id
	join moodle.mdl_course_categories cc on c.category=cc.id
	join moodle.mdl_user u on ra.userid=u.id
	where cc.parent=0 and c.category!=0
	group by u.id, c.id) t join moodle.mdl_role r on t.sortorder=r.sortorder
	union
	select t.userid, t.firstname, t.lastname, r.id roleid, r.shortname rolename, t.categoryid, t.idnumber, t.categoryname, t.courseid, t.coursename
	from 
	(select u.id userid, u.firstname, u.lastname, ra.roleid, r.name rolename, ccp.id categoryid, ccp.idnumber, ccp.name categoryname, c.id courseid, c.fullname coursename, min(r.sortorder) sortorder
	from moodle.mdl_role_assignments ra 
	join moodle.mdl_role r on ra.roleid=r.id
	join moodle.mdl_context ct on ra.contextid=ct.id
	join moodle.mdl_course c on ct.instanceid=c.id
	join moodle.mdl_course_categories cc on c.category=cc.id
	join moodle.mdl_course_categories ccp on cc.parent=ccp.id
	join moodle.mdl_user u on ra.userid=u.id
	where cc.parent!=0 and c.category!=0
	group by u.id, c.id) t join moodle.mdl_role r on t.sortorder=r.sortorder;
END$$

DELIMITER ;

DELIMITER $$
--
-- 事件
--
CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_activetime_forum_discussions` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:30:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_activetime_forum_discussions' and status='waiting';
		call p_activetime_forum_discussions;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_activetime_forum_discussions' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_activetime_forum_discussions', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:30:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_activetime_increment` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:31:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_activetime_increment' and status='waiting';
		call p_activetime_individual_m;
		call p_activetime_individual;
		call p_activetime_individual_logcounts;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_activetime_increment' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_activetime_increment', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:31:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_activetime_resource` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:40:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_activetime_resource' and status='waiting';
		call p_activetime_resource;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_activetime_resource' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_activetime_resource', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:40:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_active_course` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:50:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_active_course' and status='waiting';
		call p_active_course;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_active_course' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_active_course', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:50:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_latest_individual_m` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:33:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_latest_individual_m' and status='waiting';
		call p_latest_individual_m;
		call p_latest_resource;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_latest_individual_m' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_latest_individual_m',unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:33:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_question_first_correct` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:34:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_question_first_correct' and status='waiting';
		call p_question_first_correct;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_question_first_correct' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_question_first_correct', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:34:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_question_summary` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:35:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_question_summary' and status='waiting';
		call p_question_summary;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_question_summary' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_question_summary',unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:35:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_total_point` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:41:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_total_point' and status='waiting';
		call p_total_point;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_total_point' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_total_point', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:41:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_user_enrol_course` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:32:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_user_enrol_course' and status='waiting';
		call p_user_enrol_course;
		call f_split_siteadmin;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_user_enrol_course' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_user_enrol_course', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:32:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_teacher_module_counts` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:36:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		UPDATE t_event_log SET begintime=unix_timestamp(SYSDATE()), status='executing' WHERE name='e_teacher_module_counts' and status='waiting';
		CALL p_teacher_module_counts;
		UPDATE t_event_log SET endtime=unix_timestamp(SYSDATE()), lasttime=unix_timestamp(SYSDATE())-begintime, status='success' WHERE name='e_teacher_module_counts' and status='executing';
		INSERT INTO t_event_log(name, scheduletime, status) VALUES('e_latest_individual_m', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:36:00'))+1*60*60*24, 'waiting');
		COMMIT;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_course_statistic` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:42:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_course_statistic' and status='waiting';
		call p_course_statistic;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_course_statistic' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_course_statistic', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:42:00'))+1*60*60*24, 'waiting');
		commit;
END$$

CREATE DEFINER=`wakey`@`10.248.53.18` EVENT `e_activetime_course` ON SCHEDULE EVERY 1 DAY STARTS '2019-04-23 07:43:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
		update t_event_log set begintime=unix_timestamp(sysdate()), status='executing' where name='e_activetime_course' and status='waiting';
		call p_activetime_course;
		update t_event_log set endtime=unix_timestamp(sysdate()), lasttime=unix_timestamp(sysdate())-begintime, status='success' where name='e_activetime_course' and status='executing';
		insert into t_event_log(name, scheduletime, status) values('e_activetime_course', unix_timestamp(concat(date_format(now(), '%Y-%m-%d '), '07:43:00'))+1*60*60*24, 'waiting');
		commit;
END$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
