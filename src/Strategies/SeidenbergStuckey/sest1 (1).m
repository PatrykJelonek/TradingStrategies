% %sest1 strategia pol¹czona ze stuckey3 i seidenb4
%stuckey s.125(Joe Krutsinger) A.Wilinski (C)2018

%v1 koncepcja
%v2 dok³adnie wg Stuckey r=0.5 
%v3 SL

%sest1 po³aczenie strategii Stuckeya i Seidenberga

clear all

FW20WS191018; %[date OHLC Vol LOP]

m=size(C);
r=0.5;
rs=0.68;
ld=0; %liczba dlugich
ls=0;
rec=-1111;
spread=1;
SL=50;
beg=100;


ld=0; %liczba dlugich
ls=0;
ll=0;
sl=0;
lo=0;
lll=0;
sss=0;
for i=beg:m(1)-1
    ll=ll+1;
    zl(i)=0;
    zs(i)=0;
    zls(i)=0;
    zss(i)=0;
    l1(i)=0;
    l2(i)=0;
    s1(i)=0;
    s2(i)=0;
    zl8(i)=0;
    zls8(i)=0;
    
    %tresci z stuckey3
    range(i)=C(i-1,3)-C(i-1,4);
    %if i>beg+100
        Rang(i)=mean(range(i-5:i));  
    stop1(i)=C(i,2)+r*Rang(i);
    
    if C(i,3)>stop1(i)
        ld=ld+1;
        l1(i)=1;
        zl(i)=C(i,5)-stop1(i)-spread;
       
    end
    if zl(i)<-SL
           zl(i)=-SL-spread;
            sl=sl+1;
    end
    stop2(i)=C(i,2)-r*Rang(i);
    if C(i,4)<stop2(i)
        ls=ls+1;
        s1(i)=1;
        zs(i)=stop2(i)-C(i,5)-spread;
    end
        if zs(i)<-SL
           zs(i)=-SL-spread;
           sl=sl+1;
        end
        
        %tresci z seidenb3:
         %range(i)=C(i-1,3)-C(i-1,4);
    stop1s(i)=C(i-1,3)+rs*range(i);
    
    if C(i,3)>stop1s(i) %&& C(i-2,6)>C(i-1,6)
        ld=ld+1;
        l2(i)=1;
        zls(i)=C(i,5)-stop1s(i)-spread;
        
    end
    if C(i,2)>stop1s(i)
        lo=lo+1;
        zls(i)=C(i,5)-C(i,2)-spread;
    end
    if stop1s(i)-C(i,4)>SL
        sl=sl+1;
          % zl(i)=-SL-spread;
    end
    
    stop2s(i)=C(i-1,4)-rs*range(i);
    if C(i,4)<stop2s(i)
        ls=ls+1;
        s2(i)=1;
        zss(i)=stop2s(i)-C(i,5)-spread;
        
    end
    if C(i,2)<stop2s(i)
        lo=lo+1;
        zss(i)=C(i,2)-C(i,5)-spread;
    end
    if C(i,3)-stop2s(i)>SL
        sl=sl+1;
         %  zs(i)=-SL-spread;
    end
    
    %nowa strategia - jednoczesne speln ienie dwoch warunków
    
    
    if l1(i)+l2(i)==2
        lll=lll+1;
        zl8(i)=C(i,5)-stop1(i)-spread;
        zls8(i)=C(i,5)-stop1s(i)-spread;
    end
        
        
 end
   
 zcuml8=cumsum(zl8);  
 zcumls8=cumsum(zls8);
 zcum8=zcuml8+zcumls8;


zsl1=cumsum(zl);
zss1=cumsum(zs);
zcum1=zsl1+zss1;

zsl2=cumsum(zls);
zss2=cumsum(zss);
zcum2=zsl2+zss2;

zcum=zcum1+zcum2;


if zcum(end)>rec
    rec=zcum(end);
    paropt=[r rs ll ld ls sl];
    zr=zcum;
    zlr1=zsl1;
    zsr1=zss1;
    zlr2=zsl2;
    zsr2=zss2;
    
end


%obl CR dl rekordowego wyniku

mdd=0;
mm=size(zr);

for j=1:mm(2)
    obni(j)=0;
    mloc(j)=max(zr(1:j));
    if zr(j)<mloc(j)
        obni(j)=mloc(j)-zr(j);
    end
end

mdd=max(obni);
calmar=zr(end)/mdd


paropt
rec

x=beg:mm(2);
mx=size(x);


figure(1)
plot(x,zlr2(mm(2)-mx(2)+1:mm(2)),'-g')
hold on


plot(x,zsr2(mm(2)-mx(2)+1:mm(2)),'-r')

hold on
plot(x,zlr1(mm(2)-mx(2)+1:mm(2)),'-b')
hold on


plot(x,zsr1(mm(2)-mx(2)+1:mm(2)),'-m')

hold on
%plot(zr)
plot(x,zr(mm(2)-mx(2)+1:mm(2)),'-k')


figure(2)
plot(stop1,'-r')
hold on
plot(stop1s,'-m')

figure(3)
plot(zcuml8,'-b')
hold on
plot(zcumls8,'-c')
hold on
plot(zcum8)

